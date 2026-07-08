<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorPricelist;
use Illuminate\Http\Request;

class VendorPricelistController extends Controller
{
    public function index()
    {
        $data = VendorPricelist::latest()->paginate(15)->withQueryString();

        $totalItem    = VendorPricelist::count();
        $totalVendor  = VendorPricelist::distinct('vendor')->count('vendor');
        $avgDiskon    = VendorPricelist::avg('diskon') ?? 0;
        $totalBarang  = VendorPricelist::distinct('nama_barang')->count('nama_barang');

        return view('admin.vendorp.index', compact(
            'data', 'totalItem', 'totalVendor', 'avgDiskon', 'totalBarang'
        ));
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        VendorPricelist::create($validated);

        return redirect()->route('vendor-pricelist.index')
            ->with('success', 'Vendor Pricelist berhasil ditambahkan.');
    }

    public function update(Request $request, VendorPricelist $vendorPricelist)
    {
        $validated = $this->validateData($request);

        $vendorPricelist->update($validated);

        return redirect()->route('vendor-pricelist.index')
            ->with('success', 'Vendor Pricelist berhasil diperbarui.');
    }

    public function destroy(VendorPricelist $vendorPricelist)
    {
        $vendorPricelist->delete();

        return redirect()->route('vendor-pricelist.index')
            ->with('success', 'Vendor Pricelist berhasil dihapus.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'vendor'          => 'required|string|max:255',
            'kode_barang'     => 'required|string|max:255',
            'nama_barang'     => 'required|string|max:255',
            'harga_per_unit'  => 'required|integer|min:0',
            'satuan'          => 'required|string|max:255',
            'diskon'          => 'nullable|numeric|min:0|max:100',
            'minimal_order'   => 'nullable|integer|min:1',
            'lead_time'       => 'nullable|integer|min:0',
            'tanggal_berlaku' => 'required|date',
        ]);
    }
}
