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
        $this->invoice = $invoice;
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
        $pdf = Pdf::loadView('admin.invoice.print', array_merge([
            'invoice' => $this->invoice,
            'setting' => $this->setting,
        ], $this->images))->setPaper('a4', 'portrait');

        return [
            Attachment::fromData(
                fn() => $pdf->output(),
                'Invoice-' . $this->invoice->invoice_no . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}
