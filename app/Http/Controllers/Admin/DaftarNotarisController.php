<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DaftarNotaris;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DaftarNotarisController extends Controller
{
    public function index()
    {
        $data  = DaftarNotaris::latest()->get();
        $total = $data->count();

        return view('admin.legal.daftar_notaris.index', compact('data', 'total'));
    }

    public function show($id)
    {
        return response()->json(DaftarNotaris::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kantor' => 'required',
            'layanan'     => 'required',
            'kontak'      => 'required',
            'email'       => 'required|email',
        ]);
        DaftarNotaris::create($request->all());
        return back()->with('success', 'Data Notaris berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $item = DaftarNotaris::findOrFail($id);
        $request->validate([
            'nama_kantor' => 'required',
            'layanan'     => 'required',
            'kontak'      => 'required',
            'email'       => 'required|email',
        ]);
        $item->update($request->all());
        return back()->with('success', 'Data Notaris berhasil diperbarui.');
    }

    public function destroy($id)
    {
        DaftarNotaris::findOrFail($id)->delete();
        return back()->with('success', 'Data Notaris berhasil dihapus.');
    }

    public function pdf(Request $request)
    {
        $data     = DaftarNotaris::latest()->get();
        $setting  = Setting::first();
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }
        $pdf = Pdf::loadView('admin.legal.daftar_notaris.pdf', compact('data', 'setting', 'logoSrc'))
            ->setPaper('a4', 'landscape');
        return $pdf->stream('daftar-notaris.pdf');
    }
}
