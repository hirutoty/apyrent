<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Purchasero;
use Illuminate\Http\Request;

class PurchaseroController extends Controller
{
    public function index()
    {
        $data = Purchasero::latest()->get();

        $statusStats = $data->groupBy('status')->map->count();

        $totalPR        = $data->count();
        $totalDisetujui = $data->where('status', 'Disetujui')->count();
        $totalPending   = $data->where('status', 'Pending')->count();
        $totalDitolak   = $data->where('status', 'Ditolak')->count();

        return view('admin.purchasero.index', compact(
            'data', 'statusStats',
            'totalPR', 'totalDisetujui', 'totalPending', 'totalDitolak'
        ));
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        // no_pr otomatis di-generate lewat Model::boot()
        Purchasero::create($validated);

        return redirect()->route('purchasero.index')
            ->with('success', 'Purchase Request berhasil ditambahkan.');
    }

    public function update(Request $request, Purchasero $purchasero)
    {
        $validated = $this->validateData($request);

        // no_pr sengaja tidak diubah saat update
        $purchasero->update($validated);

        return redirect()->route('purchasero.index')
            ->with('success', 'Purchase Request berhasil diperbarui.');
    }

    public function destroy(Purchasero $purchasero)
    {
        $purchasero->delete();

        return redirect()->route('purchasero.index')
            ->with('success', 'Purchase Request berhasil dihapus.');
    }

    /**
     * Validasi bersama untuk store & update.
     *
     * Semua field wajib diisi, KECUALI disetujui_oleh, tanggal_persetujuan,
     * dan catatan — yang hanya wajib jika status = "Disetujui".
     * Jika status bukan "Disetujui", ketiga field tersebut otomatis dikosongkan.
     */
    private function validateData(Request $request)
    {
        $rules = [
            'tanggal'             => 'required|date',
            'departemen'          => 'required|string|max:255',
            'pemohon'             => 'required|string|max:255',
            'barang_jasa'         => 'required|string|max:255',
            'kode_barang'         => 'required|string|max:255',
            'qty'                 => 'required|integer|min:1',
            'satuan'              => 'required|string|max:255',
            'alasan_permintaan'   => 'required|string|max:255',
            'status'              => 'required|in:Pending,Disetujui,Ditolak',
            'disetujui_oleh'      => 'nullable|string|max:255',
            'tanggal_persetujuan' => 'nullable|date',
            'catatan'             => 'nullable|string',
        ];

        // Jika status "Disetujui", wajib isi siapa yang menyetujui & tanggal persetujuan
        if ($request->input('status') === 'Disetujui') {
            $rules['disetujui_oleh']      = 'required|string|max:255';
            $rules['tanggal_persetujuan'] = 'required|date';
        }

        $validated = $request->validate($rules);

        // Jika bukan "Disetujui" (Pending / Ditolak), kosongkan field approval
        if ($validated['status'] !== 'Disetujui') {
            $validated['disetujui_oleh']      = null;
            $validated['tanggal_persetujuan'] = null;
        }

        return $validated;
    }
}