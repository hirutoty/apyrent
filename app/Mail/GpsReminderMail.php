<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GpsReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $gps;
    public $hari;
    public $tipe;

    public function __construct($gps, $hari, $tipe)
    {
        $this->gps = $gps;
        $this->hari = $hari;
        $this->tipe = $tipe;
    }

    public function build()
    {
        return $this->subject(
            $this->tipe == 'reminder'
                ? 'Reminder GPS Kendaraan'
                : 'GPS Kendaraan Telah Habis'
        )->view('emails.gps-reminder');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Gps Reminder Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.gps-reminder',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
