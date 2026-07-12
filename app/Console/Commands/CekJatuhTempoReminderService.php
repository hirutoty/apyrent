<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ReminderService;
use App\Models\Setting;
use App\Http\Controllers\Admin\ReminderServiceController;
use App\Mail\ReminderServiceMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class CekJatuhTempoReminderService extends Command
{
    protected $signature = 'reminder-service:cek-jatuh-tempo';

    protected $description = 'Cek reminder service yang jatuh tempo, kirim email, dan auto-create ke Mobil Bermasalah';

    public function handle()
    {
        $this->info('Mengecek jatuh tempo reminder service...');

        $setting = Setting::first();

        if (!$setting || !$setting->email) {
            $this->error('Email setting tidak ditemukan.');
            return;
        }

        $reminders = ReminderService::with('kendaraan')
            ->where('status', '!=', 'selesai')
            ->get();

        // Hari-hari yang akan dikirim reminder (sebelum jatuh tempo)
        $hariReminder = [7, 3, 1];

        $count = 0;

        foreach ($reminders as $reminder) {
            if (!$reminder->tanggal_jatuh_tempo) continue;

            $sisaHari   = $reminder->sisaHari();
            $sudahJatuh = Carbon::today()->gte(Carbon::parse($reminder->tanggal_jatuh_tempo));

            // ── KIRIM REMINDER sebelum jatuh tempo ───────────
            if (!$sudahJatuh && in_array($sisaHari, $hariReminder)) {
                Mail::to($setting->email)->send(
                    new ReminderServiceMail($reminder, $sisaHari, 'reminder')
                );
                $nopolReminder = $reminder->kendaraan->nopol ?? '-';
                $this->info("Email reminder H-{$sisaHari} terkirim: {$reminder->nama_reminder} ({$nopolReminder})");
            }

            // ── JATUH TEMPO ───────────────────────────────────
            if ($sudahJatuh) {

                // Update status jadi jatuh_tempo jika belum
                if ($reminder->status !== 'jatuh_tempo') {
                    $reminder->update(['status' => 'jatuh_tempo']);
                    $this->info("Reminder ID {$reminder->id} ({$reminder->nama_reminder}) jatuh tempo.");
                }

                // Kirim email jatuh tempo (hanya saat pertama kali jatuh tempo, sisaHari == 0)
                if ($sisaHari === 0) {
                    Mail::to($setting->email)->send(
                        new ReminderServiceMail($reminder, $sisaHari, 'jatuh_tempo')
                    );
                    $nopolJatuh = $reminder->kendaraan->nopol ?? '-';
                    $this->info("Email jatuh tempo terkirim: {$reminder->nama_reminder} ({$nopolJatuh})");
                }

                // Auto-create ke service_detail jika belum dibuat
                if (!$reminder->sudah_dibuat_masalah) {
                    ReminderServiceController::buatServiceDetail($reminder);
                    $count++;
                    $nopol = $reminder->kendaraan->nopol ?? '-';
                    $this->info("  -> Otomatis ditambahkan ke Mobil Bermasalah: {$nopol}");
                }
            }
        }

        $this->info("Selesai. {$count} reminder diproses ke Mobil Bermasalah.");
    }
}
