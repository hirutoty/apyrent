<?php

namespace App\Mail;

use App\Models\AgingAr;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AgingArReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $aging;

    public function __construct(AgingAr $aging)
    {
        $this->aging = $aging;
    }

    public function build()
    {
        return $this->subject('Reminder Pembayaran Invoice')
            ->view('emails.aging_ar_reminder');
    }
}