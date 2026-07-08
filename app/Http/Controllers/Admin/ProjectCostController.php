<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectCost;
use App\Models\IndukProyek;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ProjectCostController extends Controller
{
    public function index(Request $request)
    {
        $query = ProjectCost::latest();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('proyek', 'like', "%$q%")
                   ->orWhere('kategori_biaya', 'like', "%$q%");
            });
        }

        $data            = $query->paginate(15)->withQueryString();
        $total           = ProjectCost::count();
        $totalEfisien    = ProjectCost::where('status', 'Efisien')->count();
        $totalOverBudget = ProjectCost::where('status', 'Over Budget')->count();
        $totalNormal     = ProjectCost::where('status', 'Normal')->count();
        $proyeks         = IndukProyek::orderBy('kode')->pluck('nama_proyek', 'kode');

        return view('admin.project.cost.index', compact(
            'data', 'total', 'totalEfisien', 'totalOverBudget', 'totalNormal', 'proyeks'
        ));
    }

    public function show($id)
    {
        return response()->json(ProjectCost::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'proyek'        => 'required',
            'kategori_biaya'=> 'required',
            'estimasi'      => 'required|numeric',
            'realisasi'     => 'required|numeric',
            'status'        => 'required',
        ]);

        $data = $request->all();
        $data['selisih'] = $request->realisasi - $request->estimasi;

        ProjectCost::create($data);

        return back()->with('success', 'Project Cost berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $item = ProjectCost::findOrFail($id);

        $request->validate([
            'proyek'        => 'required',
            'kategori_biaya'=> 'required',
            'estimasi'      => 'required|numeric',
            'realisasi'     => 'required|numeric',
            'status'        => 'required',
        ]);

        $data = $request->all();
        $data['selisih'] = $request->realisasi - $request->estimasi;

        $item->update($data);

        return back()->with('success', 'Project Cost berhasil diperbarui.');
    }

    public function destroy($id)
    {
        ProjectCost::findOrFail($id)->delete();
        return back()->with('success', 'Project Cost berhasil dihapus.');
    }

    public function pdf(Request $request)
    {
        $data    = ProjectCost::latest()->get();
        $setting = Setting::first();
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.project.cost.pdf', compact('data', 'setting', 'logoSrc'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('project-cost.pdf');
    }
}
