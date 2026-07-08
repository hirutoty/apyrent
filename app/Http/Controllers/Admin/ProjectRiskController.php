<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectRisk;
use App\Models\IndukProyek;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ProjectRiskController extends Controller
{
    public function index(Request $request)
    {
        $query = ProjectRisk::latest();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('proyek', 'like', "%$q%")
                   ->orWhere('risiko', 'like', "%$q%");
            });
        }

        $data            = $query->paginate(15)->withQueryString();
        $total           = ProjectRisk::count();
        $totalTerkendali = ProjectRisk::where('status', 'Terkendali')->count();
        $totalDiajukan   = ProjectRisk::where('status', 'Diajukan')->count();
        $totalKritis     = ProjectRisk::where('status', 'Kritis')->count();
        $proyeks         = IndukProyek::orderBy('kode')->pluck('nama_proyek', 'kode');

        return view('admin.project.risk.index', compact(
            'data', 'total', 'totalTerkendali', 'totalDiajukan', 'totalKritis', 'proyeks'
        ));
    }

    public function show($id)
    {
        return response()->json(ProjectRisk::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'proyek'      => 'required',
            'risiko'      => 'required',
            'dampak'      => 'required',
            'kemungkinan' => 'required',
            'status'      => 'required',
        ]);

        ProjectRisk::create($request->all());

        return back()->with('success', 'Project Risk berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $item = ProjectRisk::findOrFail($id);

        $request->validate([
            'proyek'      => 'required',
            'risiko'      => 'required',
            'dampak'      => 'required',
            'kemungkinan' => 'required',
            'status'      => 'required',
        ]);

        $item->update($request->all());

        return back()->with('success', 'Project Risk berhasil diperbarui.');
    }

    public function destroy($id)
    {
        ProjectRisk::findOrFail($id)->delete();
        return back()->with('success', 'Project Risk berhasil dihapus.');
    }

    public function pdf(Request $request)
    {
        $data    = ProjectRisk::latest()->get();
        $setting = Setting::first();
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.project.risk.pdf', compact('data', 'setting', 'logoSrc'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('project-risk.pdf');
    }
}
