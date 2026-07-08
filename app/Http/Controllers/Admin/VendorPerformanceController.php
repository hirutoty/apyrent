<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorPerformance;
use Illuminate\Http\Request;

class VendorPerformanceController extends Controller
{
    public function index()
    {
        $data = VendorPerformance::latest()->paginate(15)->withQueryString();

        $totalVendor   = VendorPerformance::count();
        $avgKetepatan  = round(VendorPerformance::avg('ketepatan_waktu') ?? 0, 1);
        $avgKualitas   = round(VendorPerformance::avg('kualitas_barang') ?? 0, 1);
        $avgPenilaian  = round(VendorPerformance::avg('penilaian_akhir') ?? 0, 1);

        return view('admin.vendorf.index', compact(
            'data', 'totalVendor', 'avgKetepatan', 'avgKualitas', 'avgPenilaian'
        ));
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        VendorPerformance::create($validated);

        return redirect()->route('vendor-performance.index')
            ->with('success', 'Vendor Performance berhasil ditambahkan.');
    }

    public function update(Request $request, VendorPerformance $vendorPerformance)
    {
        $validated = $this->validateData($request);

        $vendorPerformance->update($validated);

        return redirect()->route('vendor-performance.index')
            ->with('success', 'Vendor Performance berhasil diperbarui.');
    }

    public function destroy(VendorPerformance $vendorPerformance)
    {
        $vendorPerformance->delete();

        return redirect()->route('vendor-performance.index')
            ->with('success', 'Vendor Performance berhasil dihapus.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'vendor'          => 'required|string|max:255',
            'total_order'     => 'required|integer|min:0',
            'ketepatan_waktu' => 'required|numeric|min:0|max:100',
            'kualitas_barang' => 'required|numeric|min:0|max:100',
            'komplain'        => 'required|integer|min:0',
            'penilaian_akhir' => 'required|numeric|min:0|max:100',
            'catatan'         => 'nullable|string',
        ]);
    }
}
