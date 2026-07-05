<?php

namespace App\Console\Commands;

use App\Mail\GpsReminderMail;
use App\Models\GpsKendaraan;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ReminderGpsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reminder-gps-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    
    public function handle()
    {
        $this->info('Command GPS dijalankan');

        $setting = Setting::first();

        $reminder = match ($setting->satuan_reminder) {
            'hari'   => $setting->batas_reminder,
            'minggu' => $setting->batas_reminder * 7,
            'bulan'  => $setting->batas_reminder * 30,
            'tahun'  => $setting->batas_reminder * 365,
            default  => $setting->batas_reminder,
        };

        // konsisten reminder seperti sistem lain
        $hariReminder = array_unique([
            $reminder,
            14,
            7,
            1,
        ]);

        $data = GpsKendaraan::with(['kendaraan', 'gps'])->get();

        foreach ($data as $gps) {

            $sisaHari = Carbon::today()->diffInDays(
                Carbon::parse($gps->tanggal_habis),
                false
            );

            $this->info("ID: {$gps->id}");
            $this->info("Nopol: " . ($gps->kendaraan->nopol ?? '-'));
            $this->info("Sisa Hari: {$sisaHari}");

            // 🔥 AUTO EXPIRED
            if ($sisaHari < 0 && $gps->status_sewa) {
                $gps->update([
                    'status_sewa' => 'habis'
                ]);
            }

            // 🔥 REMINDER EMAIL
            if (in_array($sisaHari, $hariReminder)) {

                $this->info('Kirim email reminder GPS');

                Mail::to($setting->email)->send(
                    new GpsReminderMail(
                        $gps,
                        $sisaHari,
                        'reminder'
                    )
                );
            }

            // 🔥 TERLAMBAT (H+1)
            if ($sisaHari == -1) {

                $this->info('Kirim email terlambat GPS');

                Mail::to($setting->email)->send(
                    new GpsReminderMail(
                        $gps,
                        1,
                        'terlambat'
                    )
                );
            }
        }

        $this->info('Selesai menjalankan reminder GPS');
    }
}
