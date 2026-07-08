<?php

namespace App\Http\Controllers\Admin\Asset;

use App\Http\Controllers\Controller;
use App\Models\PenyusutanAsset;
use Illuminate\Http\Request;

class PenyusutanAssetController extends Controller
{
    public function index()
    {
        $data                  = PenyusutanAsset::latest()->paginate(15)->withQueryString();
        $total                 = PenyusutanAsset::count();
        $totalNilaiAwal        = PenyusutanAsset::sum('nilai_awal');
        $totalAkumulasi        = PenyusutanAsset::sum('akumulasi_penyusutan');
        $totalNilaiBuku        = PenyusutanAsset::sum('nilai_buku');

        return view('admin.asset.penyusutan.index', compact(
            'data', 'total', 'totalNilaiAwal', 'totalAkumulasi', 'totalNilaiBuku'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_aset'             => 'required|string|max:255',
            'tahun'                 => 'required|digits:4|integer',
            'nilai_awal'            => 'required|numeric|min:0',
            'akumulasi_penyusutan'  => 'required|numeric|min:0',
            'nilai_buku'            => 'required|numeric|min:0',
            'metode'                => 'required|string|max:255',
            'status'                => 'required|string|max:255',
        ]);

        PenyusutanAsset::create($request->all());

        return redirect()->route('asset.penyusutan.index')
            ->with('success', 'Data Penyusutan Asset berhasil ditambahkan.');
    }

    public function update(Request $request, PenyusutanAsset $penyusutan)
    {
        $request->validate([
            'kode_aset'             => 'required|string|max:255',
            'tahun'                 => 'required|digits:4|integer',
            'nilai_awal'            => 'required|numeric|min:0',
            'akumulasi_penyusutan'  => 'required|numeric|min:0',
            'nilai_buku'            => 'required|numeric|min:0',
            'metode'                => 'required|string|max:255',
            'status'                => 'required|string|max:255',
        ]);

        $penyusutan->update($request->all());

        return redirect()->route('asset.penyusutan.index')
            ->with('success', 'Data Penyusutan Asset berhasil diperbarui.');
    }

    public function destroy(PenyusutanAsset $penyusutan)
    {
        $penyusutan->delete();

        return redirect()->route('asset.penyusutan.index')
            ->with('success', 'Data Penyusutan Asset berhasil dihapus.');
    }
}
