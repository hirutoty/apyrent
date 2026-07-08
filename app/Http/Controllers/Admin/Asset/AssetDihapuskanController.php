<?php

namespace App\Http\Controllers\Admin\Asset;

use App\Http\Controllers\Controller;
use App\Models\AssetDihapuskan;
use Illuminate\Http\Request;

class AssetDihapuskanController extends Controller
{
    public function index()
    {
        $data          = AssetDihapuskan::latest()->paginate(15)->withQueryString();
        $total         = AssetDihapuskan::count();
        $totalDijual   = AssetDihapuskan::where('status_akhir', 'Dijual')->count();
        $totalDisumbang= AssetDihapuskan::where('status_akhir', 'Disumbangkan')->count();
        $totalNilaiBuku= AssetDihapuskan::sum('nilai_buku');

        return view('admin.asset.dihapuskan.index', compact(
            'data', 'total', 'totalDijual', 'totalDisumbang', 'totalNilaiBuku'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_aset'    => 'required|string|max:255',
            'nama_aset'    => 'required|string|max:255',
            'tanggal_hapus'=> 'required|date',
            'alasan'       => 'required|string',
            'nilai_buku'   => 'required|numeric|min:0',
            'status_akhir' => 'required|string|max:255',
        ]);

        AssetDihapuskan::create($request->all());

        return redirect()->route('asset.dihapuskan.index')
            ->with('success', 'Data Asset Dihapuskan berhasil ditambahkan.');
    }

    public function update(Request $request, AssetDihapuskan $dihapuskan)
    {
        $request->validate([
            'kode_aset'    => 'required|string|max:255',
            'nama_aset'    => 'required|string|max:255',
            'tanggal_hapus'=> 'required|date',
            'alasan'       => 'required|string',
            'nilai_buku'   => 'required|numeric|min:0',
            'status_akhir' => 'required|string|max:255',
        ]);

        $dihapuskan->update($request->all());

        return redirect()->route('asset.dihapuskan.index')
            ->with('success', 'Data Asset Dihapuskan berhasil diperbarui.');
    }

    public function destroy(AssetDihapuskan $dihapuskan)
    {
        $dihapuskan->delete();

        return redirect()->route('asset.dihapuskan.index')
            ->with('success', 'Data Asset Dihapuskan berhasil dihapus.');
    }
}
