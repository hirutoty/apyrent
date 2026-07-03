<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class KirReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $kir;
    public $hari;
    public $tipe;

    public function __construct($kir, $hari, $tipe)
    {
        $this->kir = $kir;
        $this->hari = $hari;
        $this->tipe = $tipe;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->tipe == 'reminder'
                ? 'Reminder KIR Kendaraan'
                : 'KIR Kendaraan Terlambat'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.kir-reminder'
        );
    }

    public function attachments(): array
    {
        return [];
    }
}