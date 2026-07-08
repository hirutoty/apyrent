<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dropshipping;
use Illuminate\Http\Request;

class DropshippingController extends Controller
{
    public function index()
    {
        $data = Dropshipping::latest()->paginate(15)->withQueryString();

        $statusStats   = Dropshipping::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        $totalDS       = Dropshipping::count();
        $totalProses   = Dropshipping::where('status', 'Proses')->count();
        $totalKirim    = Dropshipping::where('status', 'Dikirim')->count();
        $totalSelesai  = Dropshipping::where('status', 'Selesai')->count();

        return view('admin.dropshipping.index', compact(
            'data', 'statusStats',
            'totalDS', 'totalProses', 'totalKirim', 'totalSelesai'
        ));
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        // kode_transaksi di-generate otomatis lewat Model::boot()
        Dropshipping::create($validated);

        return redirect()->route('dropshipping.index')
            ->with('success', 'Dropshipping berhasil ditambahkan.');
    }

    public function update(Request $request, Dropshipping $dropshipping)
    {
        $validated = $this->validateData($request);

        // kode_transaksi sengaja tidak diubah saat update
        $dropshipping->update($validated);

        return redirect()->route('dropshipping.index')
            ->with('success', 'Dropshipping berhasil diperbarui.');
    }

    public function destroy(Dropshipping $dropshipping)
    {
        $dropshipping->delete();

        return redirect()->route('dropshipping.index')
            ->with('success', 'Dropshipping berhasil dihapus.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'tipe'           => 'required|string|max:255',
            'vendor'         => 'required|string|max:255',
            'barang'         => 'required|string|max:255',
            'jumlah'         => 'required|integer|min:1',
            'satuan'         => 'required|string|max:255',
            'customer_akhir' => 'required|string|max:255',
            'tanggal_kirim'  => 'nullable|date',
            'status'         => 'required|in:Proses,Dikirim,Selesai',
        ]);
    }
}
