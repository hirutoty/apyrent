<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InvSummary;
use App\Models\Invoice;
use App\Models\InvoicePayment;

class InvSummarySeeder extends Seeder
{
    public function run(): void
    {
        $invoices = Invoice::with('kontrak.penawaran')->get();

        foreach ($invoices as $invoice) {
            $kontrak   = $invoice->kontrak;
            $penawaran = $kontrak?->penawaran;

            // Hitung total yang sudah dibayar dari invoice_payments
            $paid = InvoicePayment::where('invoice_id', $invoice->id)
                ->where('status', 'verified')
                ->sum('amount');

            $total     = (float) $invoice->total;
            $remaining = max(0, $total - $paid);

            $payStatus = $remaining <= 0
                ? 'lunas'
                : ($paid > 0 ? 'partial' : 'unpaid');

            InvSummary::create([
                'penawaran_id'     => $penawaran?->id,
                'kontrak_id'       => $kontrak?->id,
                'invoice_id'       => $invoice->id,
                'type'             => $invoice->type,
                'total_amount'     => $total,
                'paid_amount'      => $paid,
                'remaining_amount' => $remaining,
                'payment_status'   => $payStatus,
            ]);
        }
    }
}
