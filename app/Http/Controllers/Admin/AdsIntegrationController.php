<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdsIntegration;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AdsIntegrationController extends Controller
{
    public function index(Request $request)
    {
        $query = AdsIntegration::latest();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('id_iklan', 'like', "%$q%")
                   ->orWhere('nama_iklan', 'like', "%$q%")
                   ->orWhere('platform', 'like', "%$q%");
            });
        }

        $data           = $query->paginate(15)->withQueryString();
        $total          = AdsIntegration::count();
        $totalKlik      = AdsIntegration::sum('klik');
        $totalKonversi  = AdsIntegration::sum('konversi');
        $totalBiaya     = AdsIntegration::sum('biaya_total');
        $totalPenjualan = AdsIntegration::sum('penjualan');

        return view('admin.marketing.adsintegration.index', compact(
            'data', 'total', 'totalKlik', 'totalKonversi', 'totalBiaya', 'totalPenjualan'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_iklan'       => 'required|unique:ads_integrations,id_iklan',
            'nama_iklan'     => 'required',
            'platform'       => 'required',
            'tanggal_aktif'  => 'required|date',
            'budget_harian'  => 'required|numeric|min:0',
            'klik'           => 'required|integer|min:0',
            'konversi'       => 'required|integer|min:0',
            'biaya_total'    => 'required|numeric|min:0',
            'penjualan'      => 'required|numeric|min:0',
            'roi'            => 'required',
        ]);

        AdsIntegration::create($request->all());

        return back()->with('success', 'Data iklan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $ads = AdsIntegration::findOrFail($id);

        $request->validate([
            'id_iklan'       => 'required|unique:ads_integrations,id_iklan,' . $id,
            'nama_iklan'     => 'required',
            'platform'       => 'required',
            'tanggal_aktif'  => 'required|date',
            'budget_harian'  => 'required|numeric|min:0',
            'klik'           => 'required|integer|min:0',
            'konversi'       => 'required|integer|min:0',
            'biaya_total'    => 'required|numeric|min:0',
            'penjualan'      => 'required|numeric|min:0',
            'roi'            => 'required',
        ]);

        $ads->update($request->all());

        return back()->with('success', 'Data iklan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        AdsIntegration::findOrFail($id)->delete();

        return back()->with('success', 'Data iklan berhasil dihapus.');
    }

    public function pdf(Request $request)
    {
        $query = AdsIntegration::query();

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_iklan', 'like', "%$search%")
                  ->orWhere('id_iklan', 'like', "%$search%")
                  ->orWhere('platform', 'like', "%$search%");
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

        $pdf = Pdf::loadView('admin.marketing.adsintegration.pdf', compact('data', 'setting', 'logoSrc'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('data-ads-integration.pdf');
    }
}
