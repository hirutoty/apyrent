<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItassetManagement;
use Illuminate\Http\Request;

class ItassetManagementController extends Controller
{
    public function index()
    {
        $data        = ItassetManagement::latest()->paginate(15)->withQueryString();
        $totalAsset  = ItassetManagement::count();
        $totalAktif  = ItassetManagement::where('status', 'Aktif')->count();
        $totalRusak  = ItassetManagement::where('status', 'Rusak')->count();
        $totalDispo  = ItassetManagement::where('status', 'Disposed')->count();

        return view('admin.technology.assetm.index',
            compact('data','totalAsset','totalAktif','totalRusak','totalDispo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_aset'  => 'required|string|max:100',
            'nama_aset'  => 'required|string|max:255',
            'jenis'      => 'required|string|max:100',
            'lokasi'     => 'required|string|max:255',
            'pengguna'   => 'required|string|max:255',
            'merek'      => 'required|string|max:100',
            'tahun_beli' => 'required|integer|min:2000|max:2099',
            'status'     => 'required|string|max:50',
            'catatan'    => 'nullable|string',
        ]);

        ItassetManagement::create($request->only([
            'kode_aset','nama_aset','jenis','lokasi','pengguna','merek','tahun_beli','status','catatan'
        ]));

        return redirect()->route('assetm.index')->with('success', 'IT Asset berhasil ditambahkan.');
    }

    public function update(Request $request, ItassetManagement $assetm)
    {
        $request->validate([
            'kode_aset'  => 'required|string|max:100',
            'nama_aset'  => 'required|string|max:255',
            'jenis'      => 'required|string|max:100',
            'lokasi'     => 'required|string|max:255',
            'pengguna'   => 'required|string|max:255',
            'merek'      => 'required|string|max:100',
            'tahun_beli' => 'required|integer|min:2000|max:2099',
            'status'     => 'required|string|max:50',
            'catatan'    => 'nullable|string',
        ]);

        $assetm->update($request->only([
            'kode_aset','nama_aset','jenis','lokasi','pengguna','merek','tahun_beli','status','catatan'
        ]));

        return redirect()->route('assetm.index')->with('success', 'IT Asset berhasil diperbarui.');
    }

    public function destroy(ItassetManagement $assetm)
    {
        $assetm->delete();
        return redirect()->route('assetm.index')->with('success', 'IT Asset berhasil dihapus.');
    }
}
