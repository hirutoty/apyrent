<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\HutangVendor;
use App\Models\Setting;
use App\Mail\ReminderHutangVendorMail;
use Carbon\Carbon;

class ReminderHutangVendorCommand extends Command
{
    protected $signature = 'hutang:reminder';
    protected $description = 'Kirim reminder hutang vendor (GPS style)';

    public function handle()
    {
        $this->info('Command Hutang Vendor dijalankan');

        $setting = Setting::first();

        if (!$setting || !$setting->email) {
            $this->error('Email setting belum diisi.');
            return;
        }

        // 🔥 CONVERT SETTING (sama seperti GPS)
        $reminder = match ($setting->satuan_reminder) {
            'hari'   => $setting->batas_reminder,
            'minggu' => $setting->batas_reminder * 7,
            'bulan'  => $setting->batas_reminder * 30,
            'tahun'  => $setting->batas_reminder * 365,
            default  => $setting->batas_reminder,
        };

        // 🔥 POLA REMINDER SAMA SEPERTI GPS
        $hariReminder = array_unique([
            $reminder,
            14,
            7,
            1,
        ]);

        $data = HutangVendor::where('status', 'belum_lunas')->get();

        foreach ($data as $hutang) {

            $sisaHari = Carbon::today()->diffInDays(
                Carbon::parse($hutang->jatuh_tempo),
                false
            );

            $this->info("Vendor: {$hutang->nama_vendor}");
            $this->info("Sisa Hari: {$sisaHari}");

            // 🔥 AUTO EXPIRED (opsional status overdue)
            if ($sisaHari < 0 && $hutang->status != 'lunas') {
                $hutang->update([
                    'status' => 'terlambat'
                ]);
            }

            // 🔥 REMINDER EMAIL (GPS STYLE)
            if (in_array($sisaHari, $hariReminder)) {

                $this->info('Kirim email reminder hutang vendor');

                Mail::to($setting->email)->send(
                    new ReminderHutangVendorMail(
                        $hutang,
                        $sisaHari,
                        'reminder'
                    )
                );

                $hutang->update([
                    'last_reminder_at' => now()
                ]);
            }

            // 🔥 TERLAMBAT (H+1 style GPS)
            if ($sisaHari == -1) {

                $this->info('Kirim email terlambat hutang vendor');

                Mail::to($setting->email)->send(
                    new ReminderHutangVendorMail(
                        $hutang,
                        abs($sisaHari),
                        'terlambat'
                    )
                );

                $hutang->update([
                    'last_reminder_at' => now()
                ]);
            }
        }

        $this->info('Selesai menjalankan reminder hutang vendor');
    }
}