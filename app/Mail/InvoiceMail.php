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
    public Setting $setting;

    public function __construct(Invoice $invoice, Setting $setting)
    {

        $this->invoice = $invoice;
        $this->setting = $setting;
    }

    public function envelope(): Envelope
    {
        $setting = Setting::first();

        return new Envelope(
            from: new Address(
                $setting?->email ?? config('mail.from.address'),
                $setting?->nama_perusahaan ?? config('app.name')
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
        $pdf = Pdf::loadView('admin.invoice.print', [
            'invoice' => $this->invoice,
            'setting' => $this->setting,
        ]);

        return [
            Attachment::fromData(
                fn() => $pdf->output(),
                'Invoice-' . $this->invoice->invoice_no . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}
