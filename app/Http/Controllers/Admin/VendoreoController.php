<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendoreo;
use Illuminate\Http\Request;

class VendoreoController extends Controller
{
    public function index()
    {
        $data = Vendoreo::latest()->get();

        $statusStats = $data->groupBy('status')->map->count();

        $totalVendor    = $data->count();
        $totalAktif     = $data->where('status', 'Aktif')->count();
        $totalNonaktif  = $data->where('status', 'Tidak Aktif')->count();

        return view('admin.vendoreo.index', compact(
            'data', 'statusStats',
            'totalVendor', 'totalAktif', 'totalNonaktif'
        ));
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        // kode_vendor otomatis di-generate lewat Model::boot()
        Vendoreo::create($validated);

        return redirect()->route('vendoreo.index')
            ->with('success', 'Vendor berhasil ditambahkan.');
    }

    public function update(Request $request, Vendoreo $vendoreo)
    {
        $validated = $this->validateData($request);

        // kode_vendor sengaja tidak diubah saat update
        $vendoreo->update($validated);

        return redirect()->route('vendoreo.index')
            ->with('success', 'Vendor berhasil diperbarui.');
    }

    public function destroy(Vendoreo $vendoreo)
    {
        $vendoreo->delete();

        return redirect()->route('vendoreo.index')
            ->with('success', 'Vendor berhasil dihapus.');
    }

    /**
     * Validasi bersama untuk store & update.
     *
     * Semua field wajib diisi, KECUALI tanggal_terakhir_order dan catatan —
     * karena vendor baru bisa saja belum pernah menerima order sama sekali.
     */
    private function validateData(Request $request)
    {
        $rules = [
            'nama_vendor'             => 'required|string|max:255',
            'kategori'                => 'required|string|max:255',
            'alamat'                  => 'required|string|max:255',
            'pic_vendor'              => 'required|string|max:255',
            'no_telp'                 => 'required|string|max:30',
            'produk_jasa'             => 'required|string|max:255',
            'rating'                  => 'required|integer|min:1|max:5',
            'status'                  => 'required|in:Aktif,Tidak Aktif',
            'tanggal_terakhir_order'  => 'nullable|date',
            'catatan'                 => 'nullable|string',
        ];

        return $request->validate($rules);
    }
}