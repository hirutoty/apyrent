<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sosmedp;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SosmedpController extends Controller
{
    public function index()
    {
        $data = Sosmedp::latest()->get();
        $total = $data->count();
        $totalKlik = $data->sum('klik');
        $totalKonversi = $data->sum('konversi');
        $totalBiaya = $data->sum('total_biaya');
        $totalPenjualan = $data->sum('total_penjualan');

        return view('admin.marketing.sosmedp.index', compact(
            'data', 'total', 'totalKlik', 'totalKonversi', 'totalBiaya', 'totalPenjualan'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kampanye'      => 'required|unique:sosmedps,id_kampanye',
            'channel'          => 'required',
            'utm_source'       => 'required',
            'utm_campaign'     => 'required',
            'klik'             => 'required|integer|min:0',
            'konversi'         => 'required|integer|min:0',
            'total_biaya'      => 'required|numeric|min:0',
            'total_penjualan'  => 'required|numeric|min:0',
            'roi'              => 'required|numeric',
        ]);

        Sosmedp::create($request->all());

        return back()->with('success', 'Data sosial media berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $sosmedp = Sosmedp::findOrFail($id);

        $request->validate([
            'id_kampanye'      => 'required|unique:sosmedps,id_kampanye,' . $id,
            'channel'          => 'required',
            'utm_source'       => 'required',
            'utm_campaign'     => 'required',
            'klik'             => 'required|integer|min:0',
            'konversi'         => 'required|integer|min:0',
            'total_biaya'      => 'required|numeric|min:0',
            'total_penjualan'  => 'required|numeric|min:0',
            'roi'              => 'required|numeric',
        ]);

        $sosmedp->update($request->all());

        return back()->with('success', 'Data sosial media berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Sosmedp::findOrFail($id)->delete();

        return back()->with('success', 'Data sosial media berhasil dihapus.');
    }

    public function pdf(Request $request)
    {
        $query = Sosmedp::query();

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id_kampanye', 'like', "%$search%")
                  ->orWhere('channel', 'like', "%$search%")
                  ->orWhere('utm_source', 'like', "%$search%")
                  ->orWhere('utm_campaign', 'like', "%$search%");
            });
        }

        $data = $query->latest()->get();
        $setting = Setting::first();

        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.marketing.sosmedp.pdf', compact('data', 'setting', 'logoSrc'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('data-sosmedp.pdf');
    }
}
