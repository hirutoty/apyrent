<?php

namespace App\Console\Commands;

use App\Mail\AsuransiReminderMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\PajakReminderMail;
use App\Models\AsuransiKendaraan;
use App\Models\Setting;

class ReminderAsuransiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reminder-asuransi-command';

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
        $setting = Setting::first();

        $reminder = match ($setting->satuan_reminder) {
            'hari'   => $setting->batas_reminder,
            'minggu' => $setting->batas_reminder * 7,
            'bulan'  => $setting->batas_reminder * 30,
            'tahun'  => $setting->batas_reminder * 365,
            default  => $setting->batas_reminder,
        };

        $hariReminder = array_unique([
            $reminder,
            14,
            7,
            1,
        ]);

        $data = AsuransiKendaraan::with([
            'kendaraan',
            'asuransi',
            'jenisAsuransi',
        ])->get();

        foreach ($data as $asuransi) {

            $sisaHari = Carbon::today()->diffInDays(
                Carbon::parse($asuransi->tgl_berakhir),
                false
            );

            // otomatis expired
            if ($sisaHari < 0 && $asuransi->status_kendaraan != 'expired') {
                $asuransi->update([
                    'status_kendaraan' => 'expired'
                ]);
            }

            // reminder
            if (in_array($sisaHari, $hariReminder)) {

                Mail::to($setting->email)
                    ->send(new AsuransiReminderMail(
                        $asuransi,
                        $sisaHari,
                        'reminder'
                    ));
            }

            // terlambat 1 hari
            if ($sisaHari == -1) {

                Mail::to($setting->email)
                    ->send(new AsuransiReminderMail(
                        $asuransi,
                        1,
                        'terlambat'
                    ));
            }
        }
    }
}
