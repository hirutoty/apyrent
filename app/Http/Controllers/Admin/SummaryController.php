<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\InvSummary;
use App\Models\InvPenawaran;
use App\Models\InvKontrak;
use App\Models\Invoice;

class SummaryController extends Controller
{
    /**
     * Display listing
     */
    public function index(Request $request)
    {
        $query = InvSummary::with([
            'penawaran',
            'kontrak',
            'invoice'
        ])->latest();

        if ($request->search) {

            $query->whereHas('invoice', function ($q) use ($request) {

                $q->where('invoice_no', 'like', '%' . $request->search . '%')
                    ->orWhere('customer_name', 'like', '%' . $request->search . '%');

            })->orWhere('type', 'like', '%' . $request->search . '%');
        }

        $summaries = $query
            ->paginate(10)
            ->withQueryString();

        $penawarans = InvPenawaran::latest()->get();
        $kontraks   = InvKontrak::latest()->get();
        $invoices   = Invoice::latest()->get();

        return view('admin.summary.index', compact(
            'summaries',
            'penawarans',
            'kontraks',
            'invoices'
        ));
    }

    /**
     * Store
     */
    public function store(Request $request)
    {
        $request->validate([

            'penawaran_id' => 'nullable|exists:inv_penawarans,id',
            'kontrak_id'   => 'nullable|exists:inv_kontraks,id',
            'invoice_id'   => 'nullable|exists:invoices,id',

            'type' => 'required',

            'total_amount' => 'required|numeric|min:0',

            'paid_amount' => 'required|numeric|min:0',
        ]);

        $remaining = $request->total_amount - $request->paid_amount;

        if ($remaining <= 0) {
            $status = 'Paid';
            $remaining = 0;
        } elseif ($request->paid_amount == 0) {
            $status = 'Unpaid';
        } else {
            $status = 'Partial';
        }

        InvSummary::create([

            'penawaran_id' => $request->penawaran_id,
            'kontrak_id'   => $request->kontrak_id,
            'invoice_id'   => $request->invoice_id,

            'type' => $request->type,

            'total_amount' => $request->total_amount,

            'paid_amount' => $request->paid_amount,

            'remaining_amount' => $remaining,

            'payment_status' => $status,

        ]);

        return redirect()
            ->route('summary.index')
            ->with('success', 'Summary berhasil ditambahkan.');
    }

    /**
     * Edit (AJAX)
     */
    public function edit(Request $request, $id)
    {
        $summary = InvSummary::findOrFail($id);

        if ($request->ajax()) {
            return response()->json($summary);
        }

        return view('admin.summary.edit', compact('summary'));
    }

    /**
     * Update
     */
    public function update(Request $request, $id)
    {
        $summary = InvSummary::findOrFail($id);

        $request->validate([

            'penawaran_id' => 'nullable|exists:inv_penawarans,id',
            'kontrak_id'   => 'nullable|exists:inv_kontraks,id',
            'invoice_id'   => 'nullable|exists:invoices,id',

            'type' => 'required',

            'total_amount' => 'required|numeric|min:0',

            'paid_amount' => 'required|numeric|min:0',
        ]);

        $remaining = $request->total_amount - $request->paid_amount;

        if ($remaining <= 0) {
            $status = 'Paid';
            $remaining = 0;
        } elseif ($request->paid_amount == 0) {
            $status = 'Unpaid';
        } else {
            $status = 'Partial';
        }

        $summary->update([

            'penawaran_id' => $request->penawaran_id,
            'kontrak_id'   => $request->kontrak_id,
            'invoice_id'   => $request->invoice_id,

            'type' => $request->type,

            'total_amount' => $request->total_amount,

            'paid_amount' => $request->paid_amount,

            'remaining_amount' => $remaining,

            'payment_status' => $status,

        ]);

        return redirect()
            ->route('summary.index')
            ->with('success', 'Summary berhasil diperbarui.');
    }

    /**
     * Delete
     */
    public function destroy($id)
    {
        InvSummary::findOrFail($id)->delete();

        return redirect()
            ->route('summary.index')
            ->with('success', 'Summary berhasil dihapus.');
    }
}