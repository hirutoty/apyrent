<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\InvSummary;
use App\Models\Keuangan;
use App\Models\Bukubesar;

class PaymentsController extends Controller
{
    /**
     * Display listing.
     */
    public function index(Request $request)
    {
        $query = InvoicePayment::with('invoice')->latest('id');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('transaction_id', 'like', '%' . $request->search . '%')
                    ->orWhere('method', 'like', '%' . $request->search . '%')
                    ->orWhere('status', 'like', '%' . $request->search . '%')
                    ->orWhereHas('invoice', function ($i) use ($request) {
                        $i->where('invoice_no', 'like', '%' . $request->search . '%')
                            ->orWhere('customer_name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        $payments = $query
            ->paginate(10)
            ->withQueryString();

        $invoices = Invoice::orderBy('invoice_no')->get();

        return view(
            'admin.payments.index',
            compact('payments', 'invoices')
        );
    }

    /**
     * Generate Transaction ID
     * TRX-20260626-00001
     */
    private function generateTransactionId(): string
    {
        $prefix = 'TRX-' . now()->format('Ymd') . '-';

        $last = InvoicePayment::where('transaction_id', 'like', $prefix . '%')
            ->latest('id')
            ->first();

        $number = 1;

        if ($last) {
            $number = intval(substr($last->transaction_id, -5)) + 1;
        }

        return $prefix . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    // =========================================================================
    // P0 #1 — Recalculate saldo keuangan dari baris $fromId ke bawah
    // =========================================================================

    /**
     * Hitung ulang kolom saldo di tabel keuangans mulai dari row dengan id = $fromId.
     * Saldo awal diambil dari row tepat sebelum $fromId; jika tidak ada, dimulai dari 0.
     *
     * @param int|null $fromId  id Keuangan pertama yang perlu dihitung ulang.
     *                          Jika null maka hitung ulang seluruh tabel.
     */
    public static function recalculateKeuanganSaldo(?int $fromId): void
    {
        // Saldo awal = saldo baris tepat sebelum fromId, atau 0
        if ($fromId !== null) {
            $saldoAwal = DB::table('keuangans')
                ->where('id', '<', $fromId)
                ->orderBy('id', 'desc')
                ->value('saldo') ?? 0;
        } else {
            $saldoAwal = 0;
        }

        // Ambil semua baris mulai dari fromId, urut by id ASC
        $rows = DB::table('keuangans')
            ->when($fromId !== null, fn ($q) => $q->where('id', '>=', $fromId))
            ->orderBy('id', 'asc')
            ->get(['id', 'pemasukan', 'pengeluaran']);

        $saldo = (float) $saldoAwal;

        foreach ($rows as $row) {
            $saldo = $saldo + (float) $row->pemasukan - (float) $row->pengeluaran;

            DB::table('keuangans')
                ->where('id', $row->id)
                ->update(['saldo' => $saldo]);
        }
    }

    // =========================================================================
    // P1 #2 — Recalculate saldo buku besar dari baris $fromId ke bawah
    // =========================================================================

    /**
     * Hitung ulang kolom saldo di tabel bukubesars mulai dari row dengan id = $fromId.
     * Saldo awal diambil dari row tepat sebelum $fromId; jika tidak ada, dimulai dari 0.
     *
     * @param int|null $fromId  id Bukubesar pertama yang perlu dihitung ulang.
     *                          Jika null maka hitung ulang seluruh tabel.
     */
    public static function recalculateBukubesarSaldo(?int $fromId): void
    {
        // Saldo awal = saldo baris tepat sebelum fromId, atau 0
        if ($fromId !== null) {
            $saldoAwal = DB::table('bukubesars')
                ->where('id', '<', $fromId)
                ->orderBy('id', 'desc')
                ->value('saldo') ?? 0;
        } else {
            $saldoAwal = 0;
        }

        // Ambil semua baris mulai dari fromId, urut by id ASC
        $rows = DB::table('bukubesars')
            ->when($fromId !== null, fn ($q) => $q->where('id', '>=', $fromId))
            ->orderBy('id', 'asc')
            ->get(['id', 'kredit', 'debit']);

        $saldo = (float) $saldoAwal;

        foreach ($rows as $row) {
            $saldo = $saldo + (float) $row->kredit - (float) $row->debit;

            DB::table('bukubesars')
                ->where('id', $row->id)
                ->update(['saldo' => $saldo]);
        }
    }

    // =========================================================================
    // Sync summary (P0 #4, P0 #5, MEDIUM #1)
    // =========================================================================

    /**
     * Sinkronisasi paid_amount di InvSummary dan payment_status + status di Invoice
     * berdasarkan total semua payment Verified untuk invoice tertentu.
     */
    private function syncSummary(int $invoiceId): void
    {
        // Total semua payment Verified untuk invoice ini
        $totalVerified = InvoicePayment::where('invoice_id', $invoiceId)
            ->where('status', 'Verified')
            ->sum('amount');

        // P0 #4: gunakan computeTotal() bukan kolom total
        $invoice = Invoice::find($invoiceId);

        if (! $invoice) {
            return;
        }

        // P0 #4 — invoice total via computeTotal()
        $invoiceTotal = $invoice->computeTotal();
        $remaining    = max(0, $invoiceTotal - $totalVerified);

        // MEDIUM #1 — tentukan status konsisten untuk KEDUA kolom
        if ($totalVerified <= 0) {
            $paymentStatus = 'unpaid';   // kolom payment_status
            $statusKolom   = 'draft';    // kolom status
        } elseif ($remaining <= 0) {
            $paymentStatus = 'paid';
            $statusKolom   = 'lunas';
        } else {
            $paymentStatus = 'partial';
            $statusKolom   = 'partial';
        }

        // P0 #5 — upsert InvSummary (updateOrCreate menggantikan find + conditional update)
        InvSummary::updateOrCreate(
            ['invoice_id' => $invoiceId],
            [
                'total_amount'     => $invoiceTotal,
                'paid_amount'      => $totalVerified,
                'remaining_amount' => $remaining,
                'payment_status'   => $paymentStatus,
            ]
        );

        // MEDIUM #1 — update kedua kolom di tabel invoices secara konsisten
        $invoice->update([
            'payment_status' => $paymentStatus,
            'status'         => $statusKolom,
        ]);
    }

    // =========================================================================
    // Finance transaction (P0 #2, P0 #3, FIX #15)
    // =========================================================================

    /**
     * Simpan transaksi keuangan.
     * HARUS dipanggil di dalam DB::transaction agar lockForUpdate efektif.
     */
    private function createFinanceTransaction(InvoicePayment $payment): void
    {
        // Cegah duplikasi
        if (Keuangan::where('reference', $payment->transaction_id)->exists()) {
            return;
        }

        // P0 #3 — gunakan lockForUpdate untuk mencegah race condition
        $saldoTerakhir = DB::table('keuangans')
            ->lockForUpdate()
            ->orderBy('id', 'desc')
            ->value('saldo') ?? 0;

        $saldoBaru = (float) $saldoTerakhir + (float) $payment->amount;

        // FIX #15 — tambah 'sumber' => 'auto'
        Keuangan::create([
            'tanggal'     => $payment->payment_date,
            'reference'   => $payment->transaction_id,
            'user_id'     => auth()->id(),
            'kategori'    => 'Payment Invoice',
            'metode'      => $payment->method,
            'keterangan'  => 'Pembayaran Invoice ' . optional($payment->invoice)->invoice_no,
            'pemasukan'   => $payment->amount,
            'pengeluaran' => 0,
            'saldo'       => $saldoBaru,
            'sumber'      => 'auto',
        ]);
    }

    // =========================================================================
    // Buku Besar helpers (P1 #2, FIX #7, #8, #9)
    // =========================================================================

    /**
     * Auto-posting ke Buku Besar saat payment Verified.
     * kategori = Pendapatan, kredit = amount (uang masuk).
     * HARUS dipanggil di dalam DB::transaction agar lockForUpdate efektif.
     */
    private function postBukuBesar(InvoicePayment $payment): void
    {
        // Cegah duplikasi
        if (Bukubesar::where('kode_jurnal', $payment->transaction_id)->exists()) {
            return;
        }

        // FIX #7 — lockForUpdate untuk saldo buku besar
        $saldoTerakhir = DB::table('bukubesars')
            ->lockForUpdate()
            ->orderBy('id', 'desc')
            ->value('saldo') ?? 0;

        $saldoBaru = (float) $saldoTerakhir + (float) $payment->amount;

        Bukubesar::create([
            'kode_jurnal' => $payment->transaction_id,
            'transaksi'   => 'Pembayaran Invoice ' . optional($payment->invoice)->invoice_no,
            'kategori'    => 'Pendapatan',
            'tanggal'     => $payment->payment_date,
            'debit'       => 0,
            'kredit'      => $payment->amount,
            'saldo'       => $saldoBaru,
            'aktivitas'   => 'Operasi',
            'keterangan'  => 'Auto-posting: Pembayaran Invoice '
                             . optional($payment->invoice)->invoice_no
                             . ' dari ' . optional($payment->invoice)->customer_name,
        ]);
    }

    /**
     * Update jurnal Buku Besar yang sudah ada (saat payment diubah).
     * FIX #8 — setelah update jurnal, panggil recalculateBukubesarSaldo.
     */
    private function updateBukuBesar(InvoicePayment $payment): void
    {
        $jurnal = Bukubesar::where('kode_jurnal', $payment->transaction_id)->first();

        if ($jurnal) {
            $jurnal->update([
                'transaksi'  => 'Pembayaran Invoice ' . optional($payment->invoice)->invoice_no,
                'tanggal'    => $payment->payment_date,
                'kredit'     => $payment->amount,
                'keterangan' => 'Auto-posting: Pembayaran Invoice '
                                . optional($payment->invoice)->invoice_no
                                . ' dari ' . optional($payment->invoice)->customer_name,
            ]);

            // FIX #8 — recalculate saldo buku besar mulai dari baris ini
            self::recalculateBukubesarSaldo($jurnal->id);
        } else {
            // Belum ada, buat baru
            $this->postBukuBesar($payment);
        }
    }

    /**
     * Hapus jurnal Buku Besar terkait payment.
     * FIX #9 — simpan id jurnal sebelum delete, lalu recalculate saldo.
     */
    private function deleteBukuBesar(string $transactionId): void
    {
        $jurnal = Bukubesar::where('kode_jurnal', $transactionId)->first();

        if (! $jurnal) {
            return;
        }

        // FIX #9 — simpan id sebelum delete agar recalculate mulai dari posisi yang tepat
        $jurnalId = $jurnal->id;

        $jurnal->delete();

        self::recalculateBukubesarSaldo($jurnalId);
    }

    // =========================================================================
    // Export
    // =========================================================================

    public function exportExcel(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PaymentsExport($request->search),
            'Payments-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    // =========================================================================
    // Store (MEDIUM #6 overpayment validation)
    // =========================================================================

    /**
     * Store
     */
    public function store(Request $request)
    {
        $request->validate([
            'invoice_id'      => 'required|exists:invoices,id',
            'amount'          => 'required|numeric|min:1',
            'payment_date'    => 'required|date',
            'method'          => 'required|string|max:100',
            'status'          => 'required|in:Pending,Verified,Rejected',
            'file_pembayaran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        // MEDIUM #6 — validasi overpayment sebelum simpan
        if ($request->status === 'Verified') {
            $invoice = Invoice::findOrFail($request->invoice_id);

            $invoiceTotal = $invoice->computeTotal();

            $alreadyPaid = InvoicePayment::where('invoice_id', $request->invoice_id)
                ->where('status', 'Verified')
                ->sum('amount');

            $remaining = $invoiceTotal - (float) $alreadyPaid;

            if ((float) $request->amount > $remaining) {
                return back()
                    ->withInput()
                    ->with('error', 'Jumlah pembayaran melebihi sisa tagihan. Sisa tagihan: ' . number_format($remaining, 0, ',', '.'));
            }
        }

        DB::beginTransaction();

        try {
            $data = $request->all();

            $data['transaction_id'] = $this->generateTransactionId();

            // Upload file
            if ($request->hasFile('file_pembayaran')) {
                $file     = $request->file('file_pembayaran');
                $namaFile = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/payment'), $namaFile);
                $data['file_pembayaran'] = 'uploads/payment/' . $namaFile;
            }

            $payment = InvoicePayment::create($data);

            // Jika Verified: catat keuangan & buku besar
            if ($payment->status === 'Verified') {
                $payment->load('invoice');
                $this->createFinanceTransaction($payment);
                $this->postBukuBesar($payment);
            }

            // Sync InvSummary & Invoice payment_status + status
            $this->syncSummary($payment->invoice_id);

            DB::commit();

            return redirect()
                ->route('payments.index')
                ->with('success', 'Pembayaran berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();

            // MEDIUM #7 — gunakan file_exists + unlink bukan Storage::disk
            if (! empty($data['file_pembayaran']) && file_exists(public_path($data['file_pembayaran']))) {
                unlink(public_path($data['file_pembayaran']));
            }

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    // =========================================================================
    // Edit
    // =========================================================================

    /**
     * Edit
     */
    public function edit(Request $request, string $id)
    {
        $payment = InvoicePayment::findOrFail($id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($payment);
        }

        $invoices = Invoice::orderBy('invoice_no')->get();

        return view('admin.payment.edit', compact('payment', 'invoices'));
    }

    // =========================================================================
    // Update (FIX #10, #11, #12, #13, #14)
    // =========================================================================

    /**
     * Update
     */
    public function update(Request $request, string $id)
    {
        $payment = InvoicePayment::findOrFail($id);

        $request->validate([
            'invoice_id'      => 'required|exists:invoices,id',
            'amount'          => 'required|numeric|min:1',
            'payment_date'    => 'required|date',
            'method'          => 'required|string|max:100',
            'status'          => 'required|in:Pending,Verified,Rejected',
            'file_pembayaran' => 'nullable|file|max:4096',
        ]);

        // MEDIUM #6 — validasi overpayment sebelum simpan
        if ($request->status === 'Verified') {
            $invoice = Invoice::findOrFail($request->invoice_id);

            $invoiceTotal = $invoice->computeTotal();

            // Jumlah sudah dibayar kecuali payment yang sedang diedit
            $alreadyPaid = InvoicePayment::where('invoice_id', $request->invoice_id)
                ->where('status', 'Verified')
                ->where('id', '!=', $payment->id)
                ->sum('amount');

            $remaining = $invoiceTotal - (float) $alreadyPaid;

            if ((float) $request->amount > $remaining) {
                return back()
                    ->withInput()
                    ->with('error', 'Jumlah pembayaran melebihi sisa tagihan. Sisa tagihan: ' . number_format($remaining, 0, ',', '.'));
            }
        }

        DB::beginTransaction();

        try {
            $data = $request->except('file_pembayaran');

            // Upload file baru
            if ($request->hasFile('file_pembayaran')) {
                // MEDIUM #7 — hapus file lama dengan file_exists + unlink
                if ($payment->file_pembayaran && file_exists(public_path($payment->file_pembayaran))) {
                    unlink(public_path($payment->file_pembayaran));
                }

                $file     = $request->file('file_pembayaran');
                $namaFile = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/payment'), $namaFile);
                $data['file_pembayaran'] = 'uploads/payment/' . $namaFile;
            }

            $payment->update($data);

            // ---------------------------------------------------------------
            // Sinkronisasi keuangan
            // ---------------------------------------------------------------

            $finance = Keuangan::where('reference', $payment->transaction_id)->first();

            if ($payment->status === 'Verified') {

                if ($finance) {
                    // Update transaksi keuangan yang sudah ada
                    $finance->update([
                        'tanggal'    => $payment->payment_date,
                        'metode'     => $payment->method,
                        'keterangan' => 'Pembayaran Invoice ' . optional($payment->invoice)->invoice_no,
                        'pemasukan'  => $payment->amount,
                    ]);

                    // FIX #13 — recalculate saldo keuangan setelah update
                    self::recalculateKeuanganSaldo($finance->id);
                } else {
                    // Belum ada -> buat baru
                    $payment->load('invoice');
                    $this->createFinanceTransaction($payment);
                }

                // Auto-posting / update Buku Besar
                $payment->load('invoice');
                $this->updateBukuBesar($payment);

            } else {
                // Jika bukan Verified: hapus transaksi keuangan dan buku besar

                if ($finance) {
                    // FIX #14 — simpan id, delete, lalu recalculate
                    $financeId = $finance->id;
                    $finance->delete();
                    self::recalculateKeuanganSaldo($financeId);
                }

                // Hapus jurnal Buku Besar (sudah include recalculate di dalam deleteBukuBesar)
                $this->deleteBukuBesar($payment->transaction_id);
            }

            // Sync InvSummary & Invoice payment_status + status
            $this->syncSummary($payment->invoice_id);

            DB::commit();

            return redirect()
                ->route('payments.index')
                ->with('success', 'Pembayaran berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    // =========================================================================
    // Destroy (FIX #11, #14)
    // =========================================================================

    /**
     * Destroy
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();

        try {
            $payment = InvoicePayment::findOrFail($id);

            // MEDIUM #7 — hapus file dengan file_exists + unlink
            if ($payment->file_pembayaran && file_exists(public_path($payment->file_pembayaran))) {
                unlink(public_path($payment->file_pembayaran));
            }

            // FIX #14 — simpan id keuangan, delete, lalu recalculate
            $finance = Keuangan::where('reference', $payment->transaction_id)->first();

            if ($finance) {
                $financeId = $finance->id;
                $finance->delete();
                self::recalculateKeuanganSaldo($financeId);
            }

            // Hapus jurnal Buku Besar (sudah include recalculate di dalam deleteBukuBesar)
            $this->deleteBukuBesar($payment->transaction_id);

            // Simpan invoice_id sebelum delete
            $invoiceId = $payment->invoice_id;

            $payment->delete();

            // Sync InvSummary & Invoice payment_status + status
            $this->syncSummary($invoiceId);

            DB::commit();

            return redirect()
                ->route('payments.index')
                ->with('success', 'Pembayaran berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', $e->getMessage());
        }
    }
}
