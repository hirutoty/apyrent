<?php

namespace App\Http\Controllers\Admin\Asset;

use App\Http\Controllers\Controller;
use App\Models\PemeliharaanAsset;
use Illuminate\Http\Request;

class PemeliharaanAssetController extends Controller
{
    public function index()
    {
        $data          = PemeliharaanAsset::latest()->paginate(15)->withQueryString();
        $total         = PemeliharaanAsset::count();
        $totalSelesai  = PemeliharaanAsset::where('status', 'Selesai')->count();
        $totalProses   = PemeliharaanAsset::where('status', 'Dalam Proses')->count();
        $totalBiaya    = PemeliharaanAsset::sum('biaya');

        return view('admin.asset.pemeliharaan.index', compact(
            'data', 'total', 'totalSelesai', 'totalProses', 'totalBiaya'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_aset'          => 'required|string|max:255',
            'tanggal_service'    => 'required|date',
            'jenis_service'      => 'required|string|max:255',
            'vendor_pic'         => 'required|string|max:255',
            'biaya'              => 'required|numeric|min:0',
            'status'             => 'required|string|max:255',
            'jadwal_selanjutnya' => 'nullable|date',
            'catatan'            => 'nullable|string',
        ]);

        PemeliharaanAsset::create($request->all());

        return redirect()->route('asset.pemeliharaan.index')
            ->with('success', 'Data Pemeliharaan Asset berhasil ditambahkan.');
    }

    public function update(Request $request, PemeliharaanAsset $pemeliharaan)
    {
        $request->validate([
            'kode_aset'          => 'required|string|max:255',
            'tanggal_service'    => 'required|date',
            'jenis_service'      => 'required|string|max:255',
            'vendor_pic'         => 'required|string|max:255',
            'biaya'              => 'required|numeric|min:0',
            'status'             => 'required|string|max:255',
            'jadwal_selanjutnya' => 'nullable|date',
            'catatan'            => 'nullable|string',
        ]);

        $pemeliharaan->update($request->all());

        return redirect()->route('asset.pemeliharaan.index')
            ->with('success', 'Data Pemeliharaan Asset berhasil diperbarui.');
    }

    public function destroy(PemeliharaanAsset $pemeliharaan)
    {
        $pemeliharaan->delete();

        return redirect()->route('asset.pemeliharaan.index')
            ->with('success', 'Data Pemeliharaan Asset berhasil dihapus.');
    }
}
