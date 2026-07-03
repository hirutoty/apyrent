<?php

namespace App\Mail;

use App\Models\ServiceHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ServiceReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $service;

    public function __construct(ServiceHistory $service)
    {
        $this->service = $service;
    }

    public function build()
    {
        return $this->subject('⚠ Alert Overservice Kendaraan')
            ->view('emails.overservice');
    }
}