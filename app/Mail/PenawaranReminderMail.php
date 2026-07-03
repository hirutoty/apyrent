<?php

namespace App\Mail;

use App\Models\InvPenawaran;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PenawaranReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $penawaran;
    public $hari;
    public $tipe;

    /**
     * Create a new message instance.
     */
    public function __construct(
        InvPenawaran $penawaran,
        $hari,
        $tipe
    ) {
        $this->penawaran = $penawaran;
        $this->hari      = $hari;
        $this->tipe      = $tipe;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject(
            $this->tipe == 'reminder'
                ? 'Reminder Penawaran'
                : 'Penawaran Telah Berakhir'
        )->view('emails.penawaran-reminder');
    }
}