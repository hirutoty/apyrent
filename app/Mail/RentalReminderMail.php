<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class RentalReminderMail extends Mailable
{
    public $rental;
    public $hari;
    public $tipe;

    public function __construct($rental, $hari, $tipe)
    {
        $this->rental = $rental;
        $this->hari = $hari;
        $this->tipe = $tipe;
    }

    public function build()
    {
        return $this->subject(
            $this->tipe == 'reminder'
                ? 'Reminder Rental Kendaraan'
                : 'Rental Kendaraan Terlambat'
        )->view('emails.rental-reminder');
    }
}