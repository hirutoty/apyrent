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
            'invoice.periodes.remaks',
        ])->latest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('invoice', function ($q2) use ($request) {
                    $q2->where('invoice_no', 'like', '%' . $request->search . '%')
                       ->orWhere('customer_name', 'like', '%' . $request->search . '%');
                })->orWhere('type', 'like', '%' . $request->search . '%');
            });
        }

        // Filter status
        if ($request->status) {
            $query->where('payment_status', $request->status);
        }

        // Statistik dari SEMUA data (bukan paginate)
        $allQuery   = InvSummary::query();
        if ($request->search) {
            $allQuery->where(function ($q) use ($request) {
                $q->whereHas('invoice', function ($q2) use ($request) {
                    $q2->where('invoice_no', 'like', '%' . $request->search . '%')
                       ->orWhere('customer_name', 'like', '%' . $request->search . '%');
                })->orWhere('type', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->status) {
            $allQuery->where('payment_status', $request->status);
        }

        $stats = [
            'total'   => (clone $allQuery)->count(),
            'paid'    => (clone $allQuery)->where('payment_status', 'Paid')->count(),
            'partial' => (clone $allQuery)->where('payment_status', 'Partial')->count(),
            'unpaid'  => (clone $allQuery)->where('payment_status', 'Unpaid')->count(),
        ];

        $summaries  = $query->paginate(10)->withQueryString();
        $penawarans = InvPenawaran::latest()->get();
        $kontraks   = InvKontrak::latest()->get();
        $invoices   = Invoice::latest()->get();

        // Sync total_amount dari computeTotal() untuk summary yang punya invoice
        // agar pajak (PPN) selalu terhitung benar sesuai remaks terkini
        foreach ($summaries as $s) {
            if ($s->invoice_id && $s->invoice) {
                $correctTotal = $s->invoice->computeTotal();
                if (abs($correctTotal - (float) $s->total_amount) > 0.01) {
                    $newRemaining = max(0, $correctTotal - (float) $s->paid_amount);
                    $s->update([
                        'total_amount'     => $correctTotal,
                        'remaining_amount' => $newRemaining,
                        'payment_status'   => $newRemaining <= 0 && $correctTotal > 0 ? 'Paid'
                            : ((float) $s->paid_amount > 0 ? 'Partial' : 'Unpaid'),
                    ]);
                }
            }
        }

        return view('admin.summary.index', compact(
            'summaries',
            'penawarans',
            'kontraks',
            'invoices',
            'stats'
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

        // Jika invoice dipilih, override total_amount dengan computeTotal()
        // agar pajak (PPN) ikut terhitung sesuai remaks invoice
        $totalAmount = (float) $request->total_amount;
        if ($request->filled('invoice_id')) {
            $invoice = Invoice::find($request->invoice_id);
            if ($invoice) {
                $totalAmount = $invoice->computeTotal();
            }
        }

        $remaining = $totalAmount - (float) $request->paid_amount;

        if ($remaining <= 0) {
            $status = 'Paid';
            $remaining = 0;
        } elseif ((float) $request->paid_amount == 0) {
            $status = 'Unpaid';
        } else {
            $status = 'Partial';
        }

        $summary->update([

            'penawaran_id' => $request->penawaran_id,
            'kontrak_id'   => $request->kontrak_id,
            'invoice_id'   => $request->invoice_id,

            'type' => $request->type,

            'total_amount' => $totalAmount,

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

    public function exportExcel(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\SummaryExport($request->status),
            'Summary-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $summaries = InvSummary::with(['penawaran', 'kontrak', 'invoice'])
            ->when($request->status, fn($q) => $q->where('payment_status', $request->status))
            ->latest()
            ->get();

        $setting = \App\Models\Setting::first();

        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $stats = [
            'total'   => $summaries->count(),
            'paid'    => $summaries->where('payment_status', 'Paid')->count(),
            'partial' => $summaries->where('payment_status', 'Partial')->count(),
            'unpaid'  => $summaries->where('payment_status', 'Unpaid')->count(),
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.summary.pdf', compact(
            'summaries', 'setting', 'logoSrc', 'stats'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('Summary-' . now()->format('Y-m-d') . '.pdf');
    }
}