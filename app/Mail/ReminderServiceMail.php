<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReminderServiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reminder;
    public $sisaHari;
    public $tipe; // 'reminder' atau 'jatuh_tempo'

    public function __construct($reminder, int $sisaHari, string $tipe = 'reminder')
    {
        $this->reminder  = $reminder;
        $this->sisaHari  = $sisaHari;
        $this->tipe      = $tipe;
    }

    public function envelope(): Envelope
    {
        $subject = $this->tipe === 'jatuh_tempo'
            ? '⚠ Reminder Service Kendaraan Jatuh Tempo'
            : '⏰ Reminder Service Kendaraan';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.reminder-service');
    }

    public function attachments(): array
    {
        return [];
    }
}
