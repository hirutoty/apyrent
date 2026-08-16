<?php

namespace App\Mail;

use App\Models\Kendaraan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class ServiceReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public Kendaraan $kendaraan;

    /** @var Collection<\App\Models\ServicePart> */
    public Collection $parts;

    public function __construct(Kendaraan $kendaraan, Collection $parts)
    {
        $this->kendaraan = $kendaraan;
        $this->parts     = $parts;
    }

    public function build()
    {
        return $this->subject('⚠ Alert Overservice Kendaraan')
            ->view('emails.overservice');
    }
}
