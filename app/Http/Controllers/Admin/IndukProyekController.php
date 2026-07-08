<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IndukProyek;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class IndukProyekController extends Controller
{
    public function index()
    {
        $data         = IndukProyek::latest()->get();
        $total        = $data->count();
        $totalBerjalan = $data->where('status', 'Berjalan')->count();
        $totalSelesai  = $data->where('status', 'Selesai')->count();
        $totalPlan     = $data->where('status', 'Plan')->count();

        return view('admin.project.induk_proyek.index', compact(
            'data', 'total', 'totalBerjalan', 'totalSelesai', 'totalPlan'
        ));
    }

    public function show($id)
    {
        return response()->json(IndukProyek::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode'          => 'required|unique:induk_proyeks,kode',
            'nama_proyek'   => 'required',
            'jenis'         => 'required',
            'pic'           => 'required',
            'status'        => 'required',
            'mulai'         => 'required|date',
            'target_selesai'=> 'required|date',
        ]);

        IndukProyek::create($request->all());

        return back()->with('success', 'Induk Proyek berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $item = IndukProyek::findOrFail($id);

        $request->validate([
            'kode'          => 'required|unique:induk_proyeks,kode,' . $id,
            'nama_proyek'   => 'required',
            'jenis'         => 'required',
            'pic'           => 'required',
            'status'        => 'required',
            'mulai'         => 'required|date',
            'target_selesai'=> 'required|date',
        ]);

        $item->update($request->all());

        return back()->with('success', 'Induk Proyek berhasil diperbarui.');
    }

    public function destroy($id)
    {
        IndukProyek::findOrFail($id)->delete();
        return back()->with('success', 'Induk Proyek berhasil dihapus.');
    }

    public function pdf(Request $request)
    {
        $query = IndukProyek::query();
        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('kode', 'like', "%$s%")
                  ->orWhere('nama_proyek', 'like', "%$s%")
                  ->orWhere('status', 'like', "%$s%");
            });
        }

        $data    = $query->latest()->get();
        $setting = Setting::first();
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.project.induk_proyek.pdf', compact('data', 'setting', 'logoSrc'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('induk-proyek.pdf');
    }
}
