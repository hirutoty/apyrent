<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoicePeriode;
use App\Models\InvoiceRemak;
use Illuminate\Http\Request;

class InvoicePeriodeController extends Controller
{
    // ============================================================
    //  PERIODE
    // ============================================================

    /** GET /admin/invoices/{invoice}/periodes — list semua periode */
    public function index(Invoice $invoice)
    {
        $periodes = $invoice->periodes()->with('remaks')->orderBy('periode_awal')->get();
        return response()->json($periodes);
    }

    /** POST /admin/invoices/{invoice}/periodes */
    public function store(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'periode_awal'  => 'required|date',
            'periode_akhir' => 'nullable|date|after_or_equal:periode_awal',
        ]);

        $periode = $invoice->periodes()->create($validated);

        return response()->json([
            'message' => 'Periode berhasil ditambahkan.',
            'periode' => $periode->load('remaks'),
        ], 201);
    }

    /** PUT /admin/invoices/{invoice}/periodes/{periode} */
    public function update(Request $request, Invoice $invoice, InvoicePeriode $periode)
    {
        abort_unless($periode->invoice_id === $invoice->id, 403);

        $validated = $request->validate([
            'periode_awal'  => 'required|date',
            'periode_akhir' => 'nullable|date|after_or_equal:periode_awal',
        ]);

        $periode->update($validated);

        return response()->json([
            'message' => 'Periode berhasil diperbarui.',
            'periode' => $periode->fresh('remaks'),
        ]);
    }

    /** DELETE /admin/invoices/{invoice}/periodes/{periode} */
    public function destroy(Invoice $invoice, InvoicePeriode $periode)
    {
        abort_unless($periode->invoice_id === $invoice->id, 403);

        // Hapus semua remaks terkait dulu
        $periode->remaks()->delete();
        $periode->delete();

        return response()->json(['message' => 'Periode berhasil dihapus.']);
    }

    // ============================================================
    //  REMAKS
    // ============================================================

    /** POST /admin/invoices/{invoice}/periodes/{periode}/remaks */
    public function storeRemak(Request $request, Invoice $invoice, InvoicePeriode $periode)
    {
        abort_unless($periode->invoice_id === $invoice->id, 403);

        $validated = $request->validate([
            'remaks' => 'required|string|max:500',
            'qty'    => 'required|integer|min:1',
            'price'  => 'required|numeric|min:0',
        ]);

        $remak = $periode->remaks()->create(array_merge($validated, [
            'invoice_id' => $invoice->id,
        ]));

        return response()->json([
            'message' => 'Remaks berhasil ditambahkan.',
            'remak'   => $remak,
        ], 201);
    }

    /** PUT /admin/invoices/{invoice}/periodes/{periode}/remaks/{remak} */
    public function updateRemak(Request $request, Invoice $invoice, InvoicePeriode $periode, InvoiceRemak $remak)
    {
        abort_unless($remak->periode_id === $periode->id, 403);
        abort_unless($periode->invoice_id === $invoice->id, 403);

        $validated = $request->validate([
            'remaks' => 'required|string|max:500',
            'qty'    => 'required|integer|min:1',
            'price'  => 'required|numeric|min:0',
        ]);

        $remak->update($validated);

        return response()->json([
            'message' => 'Remaks berhasil diperbarui.',
            'remak'   => $remak->fresh(),
        ]);
    }

    /** DELETE /admin/invoices/{invoice}/periodes/{periode}/remaks/{remak} */
    public function destroyRemak(Invoice $invoice, InvoicePeriode $periode, InvoiceRemak $remak)
    {
        abort_unless($remak->periode_id === $periode->id, 403);
        abort_unless($periode->invoice_id === $invoice->id, 403);

        $remak->delete();

        return response()->json(['message' => 'Remaks berhasil dihapus.']);
    }
}
