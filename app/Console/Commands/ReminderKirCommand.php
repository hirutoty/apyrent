<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kir;
use App\Models\Setting;
use App\Mail\KirReminderMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;


class ReminderKirCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reminder-kir-command';


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
        $this->info('Command KIR dijalankan');

        $setting = Setting::first();

        // konversi setting seperti sistem lain
        $reminder = match ($setting->satuan_reminder) {
            'hari'   => $setting->batas_reminder,
            'minggu' => $setting->batas_reminder * 7,
            'bulan'  => $setting->batas_reminder * 30,
            'tahun'  => $setting->batas_reminder * 365,
            default  => $setting->batas_reminder,
        };

        // konsisten seperti GPS / Pajak
        $hariReminder = array_unique([
            $reminder,
            14,
            7,
            1,
        ]);

        $data = Kir::with('kendaraan')->get();

        foreach ($data as $kir) {

            $sisaHari = Carbon::today()->diffInDays(
                Carbon::parse($kir->masa_berlaku),
                false
            );

            $this->info("ID: {$kir->id}");
            $this->info("Nopol: " . ($kir->kendaraan->nopol ?? '-'));
            $this->info("Sisa Hari: {$sisaHari}");

            // OPTIONAL: auto expired logic (kalau mau)
            if ($sisaHari < 0) {
                // bisa tambah status kalau ada kolom status
                // $kir->update(['status' => 'expired']);
            }

            // 🔔 REMINDER
            if (in_array($sisaHari, $hariReminder)) {

                $this->info("Kirim email reminder KIR");

                Mail::to($setting->email)->send(
                    new KirReminderMail(
                        $kir,
                        $sisaHari,
                        'reminder'
                    )
                );
            }

            // ⚠️ TERLAMBAT (H+1)
            if ($sisaHari == -1) {

                $this->info("Kirim email terlambat KIR");

                Mail::to($setting->email)->send(
                    new KirReminderMail(
                        $kir,
                        1,
                        'terlambat'
                    )
                );
            }
        }

        $this->info('Selesai reminder KIR');
    }
}
