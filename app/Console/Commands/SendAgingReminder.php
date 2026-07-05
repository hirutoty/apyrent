<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AgingAr;
use App\Models\Setting;
use App\Mail\AgingArReminderMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendAgingReminder extends Command
{
    protected $signature = 'aging:reminder';

    protected $description = 'Mengirim reminder Aging AR';

    public function handle()
{
    $setting = Setting::first();

    if (!$setting) {
        $this->error('Setting tidak ditemukan.');
        return;
    }

    $batas = $setting->batas_reminder;

    $hari = match ($setting->satuan_reminder) {
        'hari'    => $batas,
        'minggu'  => $batas * 7,
        'bulan'   => $batas * 30,
        'tahun'   => $batas * 365,
        default   => $batas,
    };

    $targetTanggal = Carbon::today()->addDays($hari);

    $agings = AgingAr::with(['member', 'invoice'])
        ->whereDate('jatuh_tempo', $targetTanggal)
        ->where('status', 'Belum Bayar')
        ->get();

    $this->info('Tanggal target : ' . $targetTanggal->format('Y-m-d'));
    $this->info('Jumlah data : ' . $agings->count());

    foreach ($agings as $aging) {

        $this->info('Invoice : ' . $aging->invoice->invoice_no);
        $this->info('Email : ' . $aging->member->email_member);

        Mail::to($aging->member->email_member)
            ->send(new AgingArReminderMail($aging));

        $this->info('Email berhasil dikirim');
    }
}
}