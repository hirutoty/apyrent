<?php

namespace App\Mail;

use App\Models\HutangVendor;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReminderHutangVendorMail extends Mailable
{
    public $hutang;
    public $hari;
    public $tipe;

    public function __construct($hutang, $hari, $tipe)
    {
        $this->hutang = $hutang;
        $this->hari = $hari;
        $this->tipe = $tipe;
    }

    public function build()
    {
        return $this->subject(
            $this->tipe == 'reminder'
                ? 'Reminder Hutang Vendor'
                : 'Hutang Vendor Terlambat'
        )->view('emails.reminder_hutang_vendor');
    }
}