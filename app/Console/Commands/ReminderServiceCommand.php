<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ServicePart;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use App\Mail\ServiceReminderMail;

class ReminderServiceCommand extends Command
{
    protected $signature = 'service:reminder-overservice';

    protected $description = 'Kirim email jika ada service part overservice';

    public function handle()
    {
        // Ambil semua part yang overservice, beserta relasi kendaraan untuk email
        $parts = ServicePart::with(['kendaraan', 'serviceHistory', 'category'])
            ->where('status_pengeluaran', 'overservice')
            ->whereIn('status', ['Terpasang', 'Proses', 'Limit'])
            ->get();

        if ($parts->isEmpty()) {
            $this->info('Tidak ada overservice.');
            return;
        }

        $setting = Setting::first();

        if (!$setting || !$setting->email) {
            $this->error('Email setting tidak ditemukan.');
            return;
        }

        // Group per kendaraan agar satu email per kendaraan (tidak banjir per-part)
        $grouped = $parts->groupBy('kendaraan_id');

        foreach ($grouped as $kendaraanId => $kendaraanParts) {
            $kendaraan = $kendaraanParts->first()->kendaraan;

            Mail::to($setting->email)
                ->send(new ServiceReminderMail($kendaraan, $kendaraanParts));

            $this->info("Email terkirim untuk kendaraan: {$kendaraan?->merk} ({$kendaraan?->nopol})");
        }

        $this->info('Selesai kirim semua overservice email.');
    }
}
