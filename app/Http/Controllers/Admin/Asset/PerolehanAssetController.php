<?php

namespace App\Http\Controllers\Admin\Asset;

use App\Http\Controllers\Controller;
use App\Models\PerolehanAsset;
use Illuminate\Http\Request;

class PerolehanAssetController extends Controller
{
    public function index()
    {
        $data         = PerolehanAsset::latest()->paginate(15)->withQueryString();
        $total        = PerolehanAsset::count();
        $totalLunas   = PerolehanAsset::where('pembayaran', 'Lunas')->count();
        $totalCicilan = PerolehanAsset::where('pembayaran', 'Cicilan')->count();
        $totalHarga   = PerolehanAsset::sum('harga');

        return view('admin.asset.perolehan.index', compact(
            'data', 'total', 'totalLunas', 'totalCicilan', 'totalHarga'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_perolehan' => 'required|date',
            'kode_aset'         => 'required|string|max:255',
            'nama_aset'         => 'required|string|max:255',
            'vendor'            => 'required|string|max:255',
            'metode_pembelian'  => 'required|string|max:255',
            'harga'             => 'required|numeric|min:0',
            'status'            => 'required|string|max:255',
            'pembayaran'        => 'required|string|max:255',
        ]);

        PerolehanAsset::create($request->all());

        return redirect()->route('asset.perolehan.index')
            ->with('success', 'Data Perolehan Asset berhasil ditambahkan.');
    }

    public function update(Request $request, PerolehanAsset $perolehan)
    {
        $request->validate([
            'tanggal_perolehan' => 'required|date',
            'kode_aset'         => 'required|string|max:255',
            'nama_aset'         => 'required|string|max:255',
            'vendor'            => 'required|string|max:255',
            'metode_pembelian'  => 'required|string|max:255',
            'harga'             => 'required|numeric|min:0',
            'status'            => 'required|string|max:255',
            'pembayaran'        => 'required|string|max:255',
        ]);

        $perolehan->update($request->all());

        return redirect()->route('asset.perolehan.index')
            ->with('success', 'Data Perolehan Asset berhasil diperbarui.');
    }

    public function destroy(PerolehanAsset $perolehan)
    {
        $perolehan->delete();

        return redirect()->route('asset.perolehan.index')
            ->with('success', 'Data Perolehan Asset berhasil dihapus.');
    }
}
