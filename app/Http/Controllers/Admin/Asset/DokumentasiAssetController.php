<?php

namespace App\Http\Controllers\Admin\Asset;

use App\Http\Controllers\Controller;
use App\Models\DokumentasiAsset;
use Illuminate\Http\Request;

class DokumentasiAssetController extends Controller
{
    public function index()
    {
        $data           = DokumentasiAsset::latest()->paginate(15)->withQueryString();
        $total          = DokumentasiAsset::count();
        $totalAdaFoto   = DokumentasiAsset::where('foto_tersimpan', true)->count();
        $totalTidakFoto = DokumentasiAsset::where('foto_tersimpan', false)->count();

        return view('admin.asset.dokumentasi.index', compact(
            'data', 'total', 'totalAdaFoto', 'totalTidakFoto'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_aset'     => 'required|string|max:255',
            'nama_aset'     => 'required|string|max:255',
            'foto_tersimpan'=> 'required|boolean',
            'lokasi_file'   => 'nullable|string|max:255',
            'catatan'       => 'nullable|string',
        ]);

        DokumentasiAsset::create($request->all());

        return redirect()->route('asset.dokumentasi.index')
            ->with('success', 'Data Dokumentasi Asset berhasil ditambahkan.');
    }

    public function update(Request $request, DokumentasiAsset $dokumentasi)
    {
        $request->validate([
            'kode_aset'     => 'required|string|max:255',
            'nama_aset'     => 'required|string|max:255',
            'foto_tersimpan'=> 'required|boolean',
            'lokasi_file'   => 'nullable|string|max:255',
            'catatan'       => 'nullable|string',
        ]);

        $dokumentasi->update($request->all());

        return redirect()->route('asset.dokumentasi.index')
            ->with('success', 'Data Dokumentasi Asset berhasil diperbarui.');
    }

    public function destroy(DokumentasiAsset $dokumentasi)
    {
        $dokumentasi->delete();

        return redirect()->route('asset.dokumentasi.index')
            ->with('success', 'Data Dokumentasi Asset berhasil dihapus.');
    }
}
