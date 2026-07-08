<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Address;

class InvoiceMail extends Mailable
{
    public Invoice $invoice;
    public ?Setting $setting;
    public array $images;

    public function __construct(Invoice $invoice, ?Setting $setting, array $images = [])
    {
        $this->invoice = $invoice->load(['periodes.remaks']);
        $this->setting = $setting;
        $this->images  = $images;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                $this->setting?->email ?? config('mail.from.address'),
                $this->setting?->nama_perusahaan ?? config('app.name')
            ),
            subject: 'Invoice ' . $this->invoice->invoice_no,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice',
            with: [
                'invoice' => $this->invoice,
                'setting' => $this->setting,
            ],
        );
    }

    public function attachments(): array
    {
        // Hitung grand total untuk terbilang
        $grandTotal = 0;
        foreach ($this->invoice->periodes as $periode) {
            foreach ($periode->remaks as $item) {
                $grandTotal += $item->subtotal ?? ($item->qty * ($item->price ?? 0));
            }
        }
        $grandTotal  = $grandTotal + floatval($this->invoice->ppn ?? 0) - floatval($this->invoice->pph ?? 0);
        $terbilang   = ucwords(trim($this->penyebut((int) $grandTotal))) . ' Rupiah';

        $pdf = Pdf::loadView('admin.invoice.print', array_merge([
            'invoice'     => $this->invoice,
            'setting'     => $this->setting,
            'grand_total' => $grandTotal,
            'terbilang'   => $terbilang,
        ], $this->images))->setPaper('a4', 'portrait');

        return [
            Attachment::fromData(
                fn() => $pdf->output(),
                'Invoice-' . $this->invoice->invoice_no . '.pdf'
            )->withMime('application/pdf'),
        ];
    }

    private function penyebut(int $n): string
    {
        $n = abs($n);
        $h = ['','Satu','Dua','Tiga','Empat','Lima','Enam','Tujuh','Delapan','Sembilan','Sepuluh','Sebelas'];
        if ($n < 12)                return ' ' . $h[$n];
        elseif ($n < 20)            return $this->penyebut($n - 10) . ' Belas';
        elseif ($n < 100)           return $this->penyebut(intdiv($n, 10)) . ' Puluh' . $this->penyebut($n % 10);
        elseif ($n < 200)           return ' Seratus' . $this->penyebut($n - 100);
        elseif ($n < 1000)          return $this->penyebut(intdiv($n, 100)) . ' Ratus' . $this->penyebut($n % 100);
        elseif ($n < 2000)          return ' Seribu' . $this->penyebut($n - 1000);
        elseif ($n < 1000000)       return $this->penyebut(intdiv($n, 1000)) . ' Ribu' . $this->penyebut($n % 1000);
        elseif ($n < 1000000000)    return $this->penyebut(intdiv($n, 1000000)) . ' Juta' . $this->penyebut($n % 1000000);
        elseif ($n < 1000000000000) return $this->penyebut(intdiv($n, 1000000000)) . ' Miliar' . $this->penyebut($n % 1000000000);
        return '';
    }
}
