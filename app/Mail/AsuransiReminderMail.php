<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AsuransiReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $asuransi;
    public $hari;
    public $tipe;

    /**
     * Create a new message instance.
     */
    public function __construct($asuransi, $hari, $tipe)
    {
        $this->asuransi = $asuransi;
        $this->hari = $hari;
        $this->tipe = $tipe;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reminder Asuransi Kendaraan',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.asuransi-reminder',
        );
    }

    public function build()
    {
        return $this->subject(
            $this->tipe == 'reminder'
                ? 'Reminder Asuransi Kendaraan'
                : 'Asuransi Kendaraan Telah Berakhir'
        )->view('emails.asuransi-reminder');
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}