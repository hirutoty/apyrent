<?php

namespace App\Http\Controllers\Admin\Asset;

use App\Http\Controllers\Controller;
use App\Models\IndukAsset;
use Illuminate\Http\Request;

class IndukAssetController extends Controller
{
    public function index()
    {
        $data         = IndukAsset::latest()->paginate(15)->withQueryString();
        $total        = IndukAsset::count();
        $totalAktif   = IndukAsset::where('status', 'Aktif')->count();
        $totalNonaktif= IndukAsset::where('status', 'Nonaktif')->count();
        $totalNilai   = IndukAsset::sum('harga_perolehan');

        return view('admin.asset.induk.index', compact(
            'data', 'total', 'totalAktif', 'totalNonaktif', 'totalNilai'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_aset'         => 'required|string|max:255|unique:induk_assets,kode_aset',
            'nama_aset'         => 'required|string|max:255',
            'kategori'          => 'required|string|max:255',
            'lokasi'            => 'required|string|max:255',
            'tanggal_perolehan' => 'required|date',
            'harga_perolehan'   => 'required|integer|min:0',
            'status'            => 'required|in:Aktif,Nonaktif',
            'pic'               => 'required|string|max:255',
            'umur_ekonomis'     => 'required|integer|min:1',
            'metode_penyusutan' => 'required|string|max:255',
        ]);

        IndukAsset::create($request->all());

        return redirect()->route('asset.induk.index')
            ->with('success', 'Data Induk Asset berhasil ditambahkan.');
    }

    public function update(Request $request, IndukAsset $induk)
    {
        $request->validate([
            'kode_aset'         => 'required|string|max:255|unique:induk_assets,kode_aset,' . $induk->id,
            'nama_aset'         => 'required|string|max:255',
            'kategori'          => 'required|string|max:255',
            'lokasi'            => 'required|string|max:255',
            'tanggal_perolehan' => 'required|date',
            'harga_perolehan'   => 'required|integer|min:0',
            'status'            => 'required|in:Aktif,Nonaktif',
            'pic'               => 'required|string|max:255',
            'umur_ekonomis'     => 'required|integer|min:1',
            'metode_penyusutan' => 'required|string|max:255',
        ]);

        $induk->update($request->all());

        return redirect()->route('asset.induk.index')
            ->with('success', 'Data Induk Asset berhasil diperbarui.');
    }

    public function destroy(IndukAsset $induk)
    {
        $induk->delete();

        return redirect()->route('asset.induk.index')
            ->with('success', 'Data Induk Asset berhasil dihapus.');
    }
}
