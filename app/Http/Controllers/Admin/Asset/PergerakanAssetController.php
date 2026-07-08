<?php

namespace App\Http\Controllers\Admin\Asset;

use App\Http\Controllers\Controller;
use App\Models\PergerakanAsset;
use Illuminate\Http\Request;

class PergerakanAssetController extends Controller
{
    public function index()
    {
        $data             = PergerakanAsset::latest()->paginate(15)->withQueryString();
        $total            = PergerakanAsset::count();
        $totalMutasi      = PergerakanAsset::where('jenis_pergerakan', 'Mutasi')->count();
        $totalPengembalian= PergerakanAsset::where('jenis_pergerakan', 'Pengembalian')->count();
        $totalPeminjaman  = PergerakanAsset::where('jenis_pergerakan', 'Peminjaman')->count();

        return view('admin.asset.pergerakan.index', compact(
            'data', 'total', 'totalMutasi', 'totalPengembalian', 'totalPeminjaman'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_aset'       => 'required|string|max:255',
            'tanggal'         => 'required|date',
            'jenis_pergerakan'=> 'required|string|max:255',
            'dari_lokasi'     => 'required|string|max:255',
            'ke_lokasi'       => 'required|string|max:255',
            'dilakukan_oleh'  => 'required|string|max:255',
            'disetujui_oleh'  => 'required|string|max:255',
            'catatan'         => 'nullable|string',
        ]);

        PergerakanAsset::create($request->all());

        return redirect()->route('asset.pergerakan.index')
            ->with('success', 'Data Pergerakan Asset berhasil ditambahkan.');
    }

    public function update(Request $request, PergerakanAsset $pergerakan)
    {
        $request->validate([
            'kode_aset'       => 'required|string|max:255',
            'tanggal'         => 'required|date',
            'jenis_pergerakan'=> 'required|string|max:255',
            'dari_lokasi'     => 'required|string|max:255',
            'ke_lokasi'       => 'required|string|max:255',
            'dilakukan_oleh'  => 'required|string|max:255',
            'disetujui_oleh'  => 'required|string|max:255',
            'catatan'         => 'nullable|string',
        ]);

        $pergerakan->update($request->all());

        return redirect()->route('asset.pergerakan.index')
            ->with('success', 'Data Pergerakan Asset berhasil diperbarui.');
    }

    public function destroy(PergerakanAsset $pergerakan)
    {
        $pergerakan->delete();

        return redirect()->route('asset.pergerakan.index')
            ->with('success', 'Data Pergerakan Asset berhasil dihapus.');
    }
}
