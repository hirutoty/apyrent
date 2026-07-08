<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Litigasi;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LitigasiController extends Controller
{
    public function index()
    {
        $data         = Litigasi::latest()->get();
        $total        = $data->count();
        $totalProses  = $data->where('status', 'Proses')->count();
        $totalSelesai = $data->where('status', 'Selesai')->count();
        $totalMediasi = $data->where('status', 'Mediasi')->count();

        return view('admin.legal.litigasi.index', compact('data', 'total', 'totalProses', 'totalSelesai', 'totalMediasi'));
    }

    public function show($id)
    {
        return response()->json(Litigasi::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kasus'          => 'required',
            'lawan'          => 'required',
            'jenis_kasus'    => 'required',
            'status'         => 'required',
            'pengacara'      => 'required',
            'tanggal_sidang' => 'required|date',
        ]);
        Litigasi::create($request->all());
        return back()->with('success', 'Data Litigasi berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $item = Litigasi::findOrFail($id);
        $request->validate([
            'kasus'          => 'required',
            'lawan'          => 'required',
            'jenis_kasus'    => 'required',
            'status'         => 'required',
            'pengacara'      => 'required',
            'tanggal_sidang' => 'required|date',
        ]);
        $item->update($request->all());
        return back()->with('success', 'Data Litigasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Litigasi::findOrFail($id)->delete();
        return back()->with('success', 'Data Litigasi berhasil dihapus.');
    }

    public function pdf(Request $request)
    {
        $data    = Litigasi::latest()->get();
        $setting = Setting::first();
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }
        $pdf = Pdf::loadView('admin.legal.litigasi.pdf', compact('data', 'setting', 'logoSrc'))
            ->setPaper('a4', 'landscape');
        return $pdf->stream('litigasi.pdf');
    }
}
