<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Rental;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\RentalReminderMail;

class ReminderRentalCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reminder-rental-command';

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
        $this->info('Command Rental dijalankan');

        $setting = Setting::first();

        if (!$setting) {
            $this->error('Setting tidak ditemukan');
            return;
        }

        $reminder = match ($setting->satuan_reminder) {
            'hari'   => $setting->batas_reminder,
            'minggu' => $setting->batas_reminder * 7,
            'bulan'  => $setting->batas_reminder * 30,
            'tahun'  => $setting->batas_reminder * 365,
            default  => $setting->batas_reminder,
        };

        $hariReminder = array_map('intval', array_unique([$reminder, 7, 1]));

        $rentals = Rental::with(['kendaraan', 'member'])
            ->whereNotNull('tanggal_selesai')
            ->where('status', 'aktif') // ⬅️ hanya aktif
            ->get();

        foreach ($rentals as $rental) {

            if ($rental->status !== 'aktif') {
                continue;
            }

            $sisaHari = (int) floor(
                Carbon::today()->diffInDays(
                    Carbon::parse($rental->tanggal_selesai),
                    false
                )
            );

            $this->info("Rental ID: {$rental->id}");
            $this->info("Sisa Hari: {$sisaHari}");

            // AUTO EXPIRED
            if ($sisaHari < 0 && $rental->status !== 'selesai') {
                $rental->update(['status' => 'selesai']);
            }

            // REMINDER
            if ($sisaHari > 0 && in_array($sisaHari, $hariReminder)) {

                $this->info("Kirim email reminder rental");

                // Kirim ke admin
                if (!empty($setting->email)) {
                    Mail::to($setting->email)->send(
                        new RentalReminderMail($rental, $sisaHari, 'reminder')
                    );
                }

                // Kirim ke member
                if (!empty($rental->member?->email_pelanggan)) {
                    Mail::to($rental->member->email_pelanggan)->send(
                        new RentalReminderMail($rental, $sisaHari, 'reminder')
                    );
                }
            }

            // TERLAMBAT
            if ($sisaHari == -1) {

                $this->info("Kirim email terlambat rental");

                // Admin
                if (!empty($setting->email)) {
                    Mail::to($setting->email)->send(
                        new RentalReminderMail($rental, 1, 'terlambat')
                    );
                }

                // Member
                if (!empty($rental->member?->email_pelanggan)) {
                    Mail::to($rental->member->email_pelanggan)->send(
                        new RentalReminderMail($rental, 1, 'terlambat')
                    );
                }
            }
        }

        $this->info('Selesai reminder rental');
    }
}

