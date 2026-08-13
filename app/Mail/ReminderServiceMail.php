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
    public $tipe;         // 'reminder' | 'jatuh_tempo' | 'part_limit'
    public $partLimitRows; // array of ['part' => ServicePart, 'reminder' => ReminderService]

    public function __construct($reminder, int $sisaHari, string $tipe = 'reminder', array $partLimitRows = [])
    {
        $this->reminder      = $reminder;
        $this->sisaHari      = $sisaHari;
        $this->tipe          = $tipe;
        $this->partLimitRows = $partLimitRows;
    }

    public function envelope(): Envelope
    {
        $subject = match ($this->tipe) {
            'jatuh_tempo' => '⚠ Reminder Service Kendaraan Jatuh Tempo',
            'part_limit'  => '🔔 Part Kendaraan Melewati Batas Interval',
            default       => '⏰ Reminder Service Kendaraan',
        };

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
