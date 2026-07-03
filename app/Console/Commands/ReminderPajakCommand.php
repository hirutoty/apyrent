<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PajakKendaraan;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\PajakReminderMail;

class ReminderPajakCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reminder-pajak-command';

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
        $this->info('Command dijalankan');
        $setting = Setting::first();

        $reminder = match ($setting->satuan_reminder) {
            'hari'   => $setting->batas_reminder,
            'minggu' => $setting->batas_reminder * 7,
            'bulan'  => $setting->batas_reminder * 30,
            'tahun'  => $setting->batas_reminder * 365,
            default  => $setting->batas_reminder,
        };

        $data = PajakKendaraan::with('kendaraan')
            ->where('status', '!=', 'sudah_bayar')
            ->get();

        foreach ($data as $pajak) {
            $this->info('ID: ' . $pajak->id);
            $this->info('Nopol: ' . $pajak->kendaraan->nopol);

            $this->info('Jumlah data: ' . $data->count());

            $sisaHari = Carbon::today()->diffInDays(
                Carbon::parse($pajak->jatuh_tempo),
                false
            );
            $this->info('Sisa Hari : ' . $sisaHari);
            $this->info('Reminder : ' . $reminder);


            // Reminder
            if ($sisaHari == $reminder) {
                $this->info('Mengirim email...');
                Mail::to($setting->email)
                    ->send(new PajakReminderMail($pajak, $sisaHari, 'reminder'));
            }

            // Tepat 1 hari terlambat
            if ($sisaHari == 1) {
                Mail::to($setting->email)
                    ->send(new PajakReminderMail($pajak, 1, 'terlambat'));
            }
        }
    }
}
