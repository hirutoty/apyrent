<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectPlanning;
use App\Models\IndukProyek;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ProjectPlanningController extends Controller
{
    public function index(Request $request)
    {
        $query = ProjectPlanning::latest();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('kode_proyek', 'like', "%$q%")
                   ->orWhere('tahapan', 'like', "%$q%")
                   ->orWhere('pic', 'like', "%$q%");
            });
        }

        $data          = $query->paginate(15)->withQueryString();
        $total         = ProjectPlanning::count();
        $totalSelesai  = ProjectPlanning::where('status', 'Selesai')->count();
        $totalBerjalan = ProjectPlanning::where('status', 'Berjalan')->count();
        $totalPlan     = ProjectPlanning::where('status', 'Plan')->count();
        $proyeks       = IndukProyek::orderBy('kode')->pluck('nama_proyek', 'kode');

        return view('admin.project.planning.index', compact(
            'data', 'total', 'totalSelesai', 'totalBerjalan', 'totalPlan', 'proyeks'
        ));
    }

    public function show($id)
    {
        return response()->json(ProjectPlanning::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_proyek' => 'required',
            'tahapan'     => 'required',
            'tgl_mulai'   => 'required|date',
            'tgl_selesai' => 'required|date',
            'pic'         => 'required',
            'status'      => 'required',
        ]);

        $data = $request->all();
        $data['durasi'] = \Carbon\Carbon::parse($request->tgl_mulai)
            ->diffInDays(\Carbon\Carbon::parse($request->tgl_selesai));

        ProjectPlanning::create($data);

        return back()->with('success', 'Project Planning berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $item = ProjectPlanning::findOrFail($id);

        $request->validate([
            'kode_proyek' => 'required',
            'tahapan'     => 'required',
            'tgl_mulai'   => 'required|date',
            'tgl_selesai' => 'required|date',
            'pic'         => 'required',
            'status'      => 'required',
        ]);

        $data = $request->all();
        $data['durasi'] = \Carbon\Carbon::parse($request->tgl_mulai)
            ->diffInDays(\Carbon\Carbon::parse($request->tgl_selesai));

        $item->update($data);

        return back()->with('success', 'Project Planning berhasil diperbarui.');
    }

    public function destroy($id)
    {
        ProjectPlanning::findOrFail($id)->delete();
        return back()->with('success', 'Project Planning berhasil dihapus.');
    }

    public function pdf(Request $request)
    {
        $data    = ProjectPlanning::latest()->get();
        $setting = Setting::first();
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.project.planning.pdf', compact('data', 'setting', 'logoSrc'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('project-planning.pdf');
    }
}
