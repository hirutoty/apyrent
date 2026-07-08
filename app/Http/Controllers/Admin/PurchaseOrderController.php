<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $data = PurchaseOrder::latest()->paginate(15)->withQueryString();

        $statusStats = PurchaseOrder::selectRaw('status_po, count(*) as total')->groupBy('status_po')->pluck('total', 'status_po');

        $totalPO       = PurchaseOrder::count();
        $totalApproved = PurchaseOrder::where('status_po', 'Approved')->count();
        $totalPending  = PurchaseOrder::where('status_po', 'Pending')->count();
        $totalClosed   = PurchaseOrder::where('status_po', 'Closed')->count();

        return view('admin.purchaseo.index', compact(
            'data', 'statusStats',
            'totalPO', 'totalApproved', 'totalPending', 'totalClosed'
        ));
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        // po_id di-generate otomatis lewat Model::boot()
        PurchaseOrder::create($validated);

        return redirect()->route('purchase-order.index')
            ->with('success', 'Purchase Order berhasil ditambahkan.');
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $this->validateData($request);

        // po_id sengaja tidak diubah saat update
        $purchaseOrder->update($validated);

        return redirect()->route('purchase-order.index')
            ->with('success', 'Purchase Order berhasil diperbarui.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->delete();

        return redirect()->route('purchase-order.index')
            ->with('success', 'Purchase Order berhasil dihapus.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'tanggal_po'     => 'required|date',
            'vendor'         => 'required|string|max:255',
            'terkait_rfq'    => 'nullable|string|max:255',
            'total_barang'   => 'required|integer|min:1',
            'total_harga'    => 'required|integer|min:0',
            'status_po'      => 'required|in:Pending,Approved,Closed',
            'tanggal_kirim'  => 'nullable|date',
            'tanggal_terima' => 'nullable|date',
            'catatan'        => 'nullable|string',
        ]);
    }
}
