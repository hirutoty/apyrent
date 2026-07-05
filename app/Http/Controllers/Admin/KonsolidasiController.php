<?php

namespace App\Http\Controllers\Admin;

use App\Exports\KonsolidasiExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LaporanKeuangan;
use App\Models\Setting;

class KonsolidasiController extends Controller
{
    public function index()
    {
        $data = LaporanKeuangan::latest()->get();
        return view('admin.konsolidasi.index', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_perusahaan' => 'required',
            'pendapatan' => 'required|numeric',
            'beban' => 'required|numeric',
            'periode' => 'required',
        ]);

        LaporanKeuangan::create([
            'nama_perusahaan' => $request->nama_perusahaan,
            'pendapatan' => $request->pendapatan,
            'beban' => $request->beban,
            'laba' => $request->pendapatan - $request->beban,
            'periode' => $request->periode,
        ]);

        return back()->with('success', 'Data berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $data = LaporanKeuangan::findOrFail($id);

        $data->update([
            'nama_perusahaan' => $request->nama_perusahaan,
            'pendapatan' => $request->pendapatan,
            'beban' => $request->beban,
            'laba' => $request->pendapatan - $request->beban,
            'periode' => $request->periode,
        ]);

        return back()->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        LaporanKeuangan::findOrFail($id)->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }

    

public function pdf(Request $request)
{
    $query = LaporanKeuangan::query();

    // 🔥 pakai filter yang sama seperti search JS
    if ($request->search) {
        $query->where('nama_perusahaan', 'like', "%{$request->search}%")
              ->orWhere('periode', 'like', "%{$request->search}%");
    }

    $data = $query->get();
    $setting = Setting::first();
        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

    $pdf = PDF::loadView('admin.konsolidasi.pdf', compact('data', 'setting', 'logoSrc'))
              ->setPaper('a4', 'landscape');

    return $pdf->stream('laporan-konsolidasi.pdf');
}


public function exportExcel(Request $request)
{
    $filename = 'laporan_keuangan_' . now()->format('Ymd_His') . '.xlsx';

    return Excel::download(
        new KonsolidasiExport($request->search),
        $filename
    );
}
}