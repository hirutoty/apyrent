<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\InvSummary;
use App\Models\Keuangan;

class PaymentsController extends Controller
{
    /**
     * Display listing.
     */
    public function index(Request $request)
    {
        $query = InvoicePayment::with('invoice')->latest();

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
            compact(
                'payments',
                'invoices'
            )
        );
    }

    /**
     * Generate Transaction ID
     * TRX-20260626-00001
     */
    private function generateTransactionId()
    {
        $prefix = 'TRX-' . now()->format('Ymd') . '-';

        $last = InvoicePayment::where(
            'transaction_id',
            'like',
            $prefix . '%'
        )
            ->latest('id')
            ->first();

        $number = 1;

        if ($last) {

            $number = intval(substr($last->transaction_id, -5)) + 1;
        }

        return $prefix . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Sinkronisasi paid_amount di InvSummary dan payment_status di Invoice
     * berdasarkan total semua payment Verified untuk invoice tertentu.
     */
    private function syncSummary(int $invoiceId): void
    {
        // Total semua payment Verified untuk invoice ini
        $totalVerified = InvoicePayment::where('invoice_id', $invoiceId)
            ->where('status', 'Verified')
            ->sum('amount');

        // Update InvSummary yang terkait invoice ini
        $summary = InvSummary::where('invoice_id', $invoiceId)->first();

        if ($summary) {
            $remaining = max(0, $summary->total_amount - $totalVerified);

            if ($totalVerified <= 0) {
                $paymentStatus = 'Unpaid';
            } elseif ($remaining <= 0) {
                $paymentStatus = 'Paid';
            } else {
                $paymentStatus = 'Partial';
            }

            $summary->update([
                'paid_amount'      => $totalVerified,
                'remaining_amount' => $remaining,
                'payment_status'   => $paymentStatus,
            ]);
        }

        // Update payment_status di tabel invoices juga
        $invoice = Invoice::find($invoiceId);
        if ($invoice) {
            $invoiceTotal = $invoice->total ?? 0;
            $remaining    = max(0, $invoiceTotal - $totalVerified);

            if ($totalVerified <= 0) {
                $paymentStatus = 'Unpaid';
            } elseif ($remaining <= 0) {
                $paymentStatus = 'Paid';
            } else {
                $paymentStatus = 'Partial';
            }

            $invoice->update(['payment_status' => $paymentStatus]);
        }
    }

    /**
     * Simpan transaksi keuangan
     */
    private function createFinanceTransaction(InvoicePayment $payment)
    {
        // Jangan sampai duplicate
        if (
            Keuangan::where(
                'reference',
                $payment->transaction_id
            )->exists()
        ) {
            return;
        }

        $saldoTerakhir = Keuangan::latest('id')->value('saldo') ?? 0;

        $saldoBaru = $saldoTerakhir + $payment->amount;

        Keuangan::create([

            'tanggal' => $payment->payment_date,

            'reference' => $payment->transaction_id,

            'user_id' => auth()->id(),

            'kategori' => 'Payment Invoice',

            'metode' => $payment->method,

            'keterangan' =>
            'Pembayaran Invoice ' .
                optional($payment->invoice)->invoice_no,

            'pemasukan' => $payment->amount,

            'pengeluaran' => 0,

            'saldo' => $saldoBaru,
        ]);
    }

    public function exportExcel(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PaymentsExport($request->search),
            'Payments-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Store
     */
    public function store(Request $request)
    {
        $request->validate([

            'invoice_id' => 'required|exists:invoices,id',

            'amount' => 'required|numeric|min:1',

            'payment_date' => 'required|date',

            'method' => 'required|string|max:100',

            'status' => 'required|in:Pending,Verified,Rejected',

            'file_pembayaran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        DB::beginTransaction();

        try {

            $data = $request->all();

            $data['transaction_id'] = $this->generateTransactionId();

            // Upload file
            if ($request->hasFile('file_pembayaran')) {

                $file = $request->file('file_pembayaran');

                $namaFile = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                $file->move(public_path('uploads/payment'), $namaFile);

                $data['file_pembayaran'] = 'uploads/payment/' . $namaFile;
            }

            $payment = InvoicePayment::create($data);

            // jika verified
            if ($payment->status == 'Verified') {

                $payment->load('invoice');

                $this->createFinanceTransaction($payment);
            }

            // Sync InvSummary & Invoice payment_status
            $this->syncSummary($payment->invoice_id);

            DB::commit();

            return redirect()
                ->route('payments.index')
                ->with(
                    'success',
                    'Pembayaran berhasil ditambahkan.'
                );
        } catch (\Exception $e) {

            DB::rollBack();

            if (!empty($data['file_pembayaran'])) {

                Storage::disk('public')
                    ->delete($data['file_pembayaran']);
            }

            return back()
                ->withInput()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }

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

        return view('admin.payment.edit', compact(
            'payment',
            'invoices'
        ));
    }

    /**
     * Update
     */
    public function update(Request $request, string $id)
    {
        $payment = InvoicePayment::findOrFail($id);

        $request->validate([

            'invoice_id' => 'required|exists:invoices,id',

            'amount' => 'required|numeric|min:1',

            'payment_date' => 'required|date',

            'method' => 'required|string|max:100',

            'status' => 'required|in:Pending,Verified,Rejected',

            'file_pembayaran' => 'nullable|file|max:4096',
        ]);

        DB::beginTransaction();

        try {

            $data = $request->except('file_pembayaran');

            // Upload file baru
            if ($request->hasFile('file_pembayaran')) {

                // Hapus file lama
                if (
                    $payment->file_pembayaran &&
                    file_exists(public_path($payment->file_pembayaran))
                ) {
                    unlink(public_path($payment->file_pembayaran));
                }

                $file = $request->file('file_pembayaran');

                $namaFile = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                $file->move(public_path('uploads/payment'), $namaFile);

                $data['file_pembayaran'] = 'uploads/payment/' . $namaFile;
            }
            $payment->update($data);

            /*
            |--------------------------------------------------------------------------
            | Sinkronisasi keuangan
            |--------------------------------------------------------------------------
            */

            $finance = Keuangan::where(
                'reference',
                $payment->transaction_id
            )->first();

            if ($payment->status == 'Verified') {

                if ($finance) {

                    // update transaksi keuangan

                    $finance->update([

                        'tanggal' => $payment->payment_date,

                        'metode' => $payment->method,

                        'keterangan' =>
                        'Pembayaran Invoice ' .
                            optional($payment->invoice)->invoice_no,

                        'pemasukan' => $payment->amount,
                    ]);
                } else {

                    // belum ada -> buat baru

                    $payment->load('invoice');

                    $this->createFinanceTransaction($payment);
                }
            } else {

                // jika bukan verified maka hapus transaksi keuangan

                if ($finance) {
                    $finance->delete();
                }
            }

            // Sync InvSummary & Invoice payment_status
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

    /**
     * Destroy
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();

        try {

            $payment = InvoicePayment::findOrFail($id);

            // hapus file

            if (
                $payment->file_pembayaran &&
                Storage::disk('public')->exists($payment->file_pembayaran)
            ) {
                Storage::disk('public')->delete($payment->file_pembayaran);
            }

            // hapus transaksi keuangan

            Keuangan::where(
                'reference',
                $payment->transaction_id
            )->delete();

            // simpan invoice_id sebelum delete
            $invoiceId = $payment->invoice_id;

            $payment->delete();

            // Sync InvSummary & Invoice payment_status
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
