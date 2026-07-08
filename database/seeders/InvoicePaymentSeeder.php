<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InvoicePayment;
use App\Models\Invoice;
use Carbon\Carbon;

class InvoicePaymentSeeder extends Seeder
{
    public function run(): void
    {
        $invoices = Invoice::all();
        $methods  = ['Transfer Bank', 'Virtual Account', 'Tunai', 'Cek/Giro', 'QRIS'];

        foreach ($invoices as $invoice) {
            $isPaid = $invoice->payment_status === 'paid';

            if ($isPaid) {
                // Pelunasan sekaligus
                InvoicePayment::create([
                    'invoice_id'      => $invoice->id,
                    'amount'          => $invoice->total,
                    'payment_date'    => Carbon::parse($invoice->invoice_date)->addDays(rand(1, 30))->toDateString(),
                    'method'          => $methods[array_rand($methods)],
                    'transaction_id'  => 'TXN-' . strtoupper(substr(md5($invoice->id . time()), 0, 10)),
                    'file_pembayaran' => null,
                    'status'          => 'verified',
                ]);
            } elseif ($invoice->status === 'partial') {
                // Bayar sebagian — 2 cicilan
                $cicilan1 = round($invoice->total * 0.5, 2);
                $cicilan2 = round($invoice->total * 0.3, 2);

                InvoicePayment::create([
                    'invoice_id'      => $invoice->id,
                    'amount'          => $cicilan1,
                    'payment_date'    => Carbon::parse($invoice->invoice_date)->addDays(rand(1, 15))->toDateString(),
                    'method'          => $methods[array_rand($methods)],
                    'transaction_id'  => 'TXN-' . strtoupper(substr(md5($invoice->id . 'a'), 0, 10)),
                    'file_pembayaran' => null,
                    'status'          => 'verified',
                ]);

                InvoicePayment::create([
                    'invoice_id'      => $invoice->id,
                    'amount'          => $cicilan2,
                    'payment_date'    => Carbon::parse($invoice->invoice_date)->addDays(rand(16, 45))->toDateString(),
                    'method'          => $methods[array_rand($methods)],
                    'transaction_id'  => 'TXN-' . strtoupper(substr(md5($invoice->id . 'b'), 0, 10)),
                    'file_pembayaran' => null,
                    'status'          => 'pending',
                ]);
            }
            // draft & overdue → belum ada pembayaran
        }
    }
}
