<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectTimeline;
use App\Models\IndukProyek;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ProjectTimelineController extends Controller
{
    public function index()
    {
        $data      = ProjectTimeline::latest()->get();
        $total     = $data->count();
        $totalScheduled = $data->where('status', 'Scheduled')->count();
        $totalBerjalan  = $data->where('status', 'Berjalan')->count();
        $totalSelesai   = $data->where('status', 'Selesai')->count();
        $proyeks   = IndukProyek::orderBy('kode')->pluck('nama_proyek', 'kode');

        return view('admin.project.timeline.index', compact(
            'data', 'total', 'totalScheduled', 'totalBerjalan', 'totalSelesai', 'proyeks'
        ));
    }

    public function show($id)
    {
        return response()->json(ProjectTimeline::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'proyek'   => 'required',
            'kegiatan' => 'required',
            'deadline' => 'required|date',
            'status'   => 'required',
        ]);

        ProjectTimeline::create($request->all());

        return back()->with('success', 'Project Timeline berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $item = ProjectTimeline::findOrFail($id);

        $request->validate([
            'proyek'   => 'required',
            'kegiatan' => 'required',
            'deadline' => 'required|date',
            'status'   => 'required',
        ]);

        $item->update($request->all());

        return back()->with('success', 'Project Timeline berhasil diperbarui.');
    }

    public function destroy($id)
    {
        ProjectTimeline::findOrFail($id)->delete();
        return back()->with('success', 'Project Timeline berhasil dihapus.');
    }

    public function pdf(Request $request)
    {
        $data    = ProjectTimeline::latest()->get();
        $setting = Setting::first();
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.project.timeline.pdf', compact('data', 'setting', 'logoSrc'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('project-timeline.pdf');
    }
}
