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
        // Base query untuk stats (tidak paginate)
        $baseQuery = InvSummary::query();

        if ($request->search) {
            $baseQuery->where(function ($q) use ($request) {
                $q->whereHas('invoice', function ($q2) use ($request) {
                    $q2->where('invoice_no', 'like', '%' . $request->search . '%')
                       ->orWhere('customer_name', 'like', '%' . $request->search . '%');
                })->orWhere('type', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->status) {
            $baseQuery->where('payment_status', $request->status);
        }

        $stats = [
            'total'   => (clone $baseQuery)->count(),
            'paid'    => (clone $baseQuery)->where('payment_status', 'Paid')->count(),
            'partial' => (clone $baseQuery)->where('payment_status', 'Partial')->count(),
            'unpaid'  => (clone $baseQuery)->where('payment_status', 'Unpaid')->count(),
        ];

        // Ambil semua summaries dengan relasi lengkap
        $allSummaries = (clone $baseQuery)
            ->with([
                'penawaran',
                'kontrak.penawaran.items',
                'invoice.periodes',
                'invoice.payments',
                'invoice.kendaraans',
                'invoice.kendaraan',
            ])
            ->latest()
            ->get();

        // Group by kontrak_id — invoice tanpa kontrak pakai key "tanpa_kontrak_{id}"
        $grouped = $allSummaries->groupBy(function ($s) {
            return $s->kontrak_id ?? 'tanpa_kontrak_' . $s->id;
        });

        // Ambil PPN dari setting sekali saja di luar loop
        $setting = \App\Models\Setting::first();
        $ppnPct  = (float) ($setting?->ppn_default ?? 0);

        // Hitung pembayaran_ke dan sisa_kali per invoice dalam tiap group
        $grouped = $grouped->map(function ($items) use ($ppnPct) {
            // Urutkan berdasarkan invoice_no agar urutan konsisten
            $items = $items->sortBy(fn($s) => optional($s->invoice)->invoice_no);

            // Total periode = durasi kontrak dalam bulan
            // Ambil dari kontrak (durasi_value + durasi_satuan), konversi ke bulan
            $kontrak = $items->first()?->kontrak;
            if ($kontrak) {
                $durVal  = (int) ($kontrak->durasi_value ?? $items->count());
                $durSat  = strtolower($kontrak->durasi_satuan ?? 'bulan');
                $totalPeriode = match ($durSat) {
                    'tahun' => $durVal * 12,
                    'hari'  => max(1, (int) round($durVal / 30)),
                    default => $durVal, // bulan
                };
            } else {
                $totalPeriode = $items->count();
            }

            // Hitung total kontrak sesungguhnya dari penawaran items × durasi per item
            // Setiap item punya durasi sendiri (Mobil A 12 bulan, Mobil B 1 bulan)
            $grandTotalKontrak = 0.0;
            if ($kontrak) {
                $penawaran = $kontrak->penawaran;
                if ($penawaran) {
                    if (! $penawaran->relationLoaded('items')) {
                        $penawaran->load('items');
                    }
                    $subtotal = 0.0;

                    $mulaiKontrak = $kontrak->perjanjian_pembayaran
                        ? \Carbon\Carbon::parse($kontrak->perjanjian_pembayaran)
                        : \Carbon\Carbon::parse($kontrak->tanggal_kontrak ?? now());

                    foreach ($penawaran->items as $item) {
                        $price    = (float) ($item->price ?? 0);
                        $qty      = (int)   ($item->qty   ?? 1);
                        $durasi   = (int)   ($item->durasi ?? 1);
                        $satuan   = strtolower(trim($item->satuan_durasi ?? 'bulan'));

                        // Konversi durasi item ke bulan
                        $itemBulan = match ($satuan) {
                            'tahun' => $durasi * 12,
                            'hari'  => max(1, (int) round($durasi / 30)),
                            default => $durasi,
                        };

                        $subtotal += $price * $qty * $itemBulan;
                    }

                    $ppnNom = round($subtotal * $ppnPct / 100);
                    $grandTotalKontrak = $subtotal + $ppnNom;
                }
            }

            // Fallback: sum dari invoice yang sudah ada
            if ($grandTotalKontrak <= 0) {
                $grandTotalKontrak = $items->sum('total_amount');
            }
            $invoiceIds = $items->pluck('invoice_id')->filter()->values()->toArray();
            $paidInvoiceCount = \App\Models\InvoicePayment::whereIn('invoice_id', $invoiceIds)
                ->where('status', 'Verified')
                ->distinct('invoice_id')
                ->count('invoice_id');

            // Hitung total PERIODE yang sudah paid (bukan jumlah invoice)
            $paidPeriodes = $items
                ->filter(fn($s) => strtolower($s->payment_status) === 'paid')
                ->sum(fn($s) => max((int)($s->periode_count ?? 1), 1));

            return $items->values()->map(function ($s, $idx) use ($totalPeriode, $paidInvoiceCount, $paidPeriodes, $grandTotalKontrak) {
                $sudahBayar = strtolower($s->payment_status) === 'paid'
                           || strtolower($s->payment_status) === 'partial';

                // Hitung jumlah periode yang dicakup invoice ini — ambil dari kolom DB jika ada,
                // fallback ke periodes count, fallback ke 1
                $jumlahPeriodeInvoice = (int) ($s->periode_count ?? 0);
                if ($jumlahPeriodeInvoice <= 0) {
                    $jumlahPeriodeInvoice = $s->invoice
                        ? max(1, $s->invoice->periodes->count())
                        : 1;
                }

                // Bayar ke: invoice ini adalah pembayaran ke-(idx+1) s/d (idx+jumlahPeriode)
                $bayarDari   = $idx + 1;
                $bayarSampai = min($idx + $jumlahPeriodeInvoice, $totalPeriode);

                $s->_pembayaran_ke  = $bayarDari === $bayarSampai
                    ? (string) $bayarDari
                    : "{$bayarDari}–{$bayarSampai}";
                $s->_sudah_bayar    = $sudahBayar;
                $s->_total_periode  = $totalPeriode;
                // Sisa = total periode - periode yang sudah tercakup invoice ini
                $s->_sisa_kali      = max(0, $totalPeriode - $bayarSampai);
                $s->_paid_count     = $paidInvoiceCount;
                $s->_paid_periodes  = $paidPeriodes;   // total periode yg sudah paid
                $s->_grand_total    = $grandTotalKontrak;
                return $s;
            });
        });

        // Paginate manual: ambil grup per halaman
        $perPage    = 5;  // 5 kontrak per halaman
        $page       = (int) ($request->page ?? 1);
        $groupKeys  = $grouped->keys();
        $totalGroups = $groupKeys->count();
        $pagedKeys  = $groupKeys->slice(($page - 1) * $perPage, $perPage);
        $pagedGroups = $grouped->only($pagedKeys->toArray());

        // Buat LengthAwarePaginator untuk pagination di view
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedGroups,
            $totalGroups,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $penawarans = InvPenawaran::latest()->get();
        $kontraks   = InvKontrak::latest()->get();
        $invoices   = Invoice::latest()->get();

        return view('admin.summary.index', compact(
            'paginator',
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

    /**
     * Hapus semua summary dalam satu kontrak sekaligus
     */
    public function destroyByKontrak($kontrak_id)
    {
        $deleted = InvSummary::where('kontrak_id', $kontrak_id)->delete();

        return redirect()
            ->route('summary.index')
            ->with('success', "Semua summary kontrak berhasil dihapus ({$deleted} data).");
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