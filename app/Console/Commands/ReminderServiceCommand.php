<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ServiceHistory;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use App\Mail\ServiceReminderMail;

class ReminderServiceCommand extends Command
{
    protected $signature = 'service:reminder-overservice';

    protected $description = 'Kirim email jika service overservice';

    public function handle()
    {
        $services = ServiceHistory::with('kendaraan')
            ->where('status_pengeluaran', 'overservice')
            ->get();

        if ($services->isEmpty()) {
            $this->info('Tidak ada overservice.');
            return;
        }

        $setting = Setting::first();

        if (!$setting || !$setting->email) {
            $this->error('Email setting tidak ditemukan.');
            return;
        }

        foreach ($services as $service) {

            Mail::to($setting->email)
                ->send(new ServiceReminderMail($service));

            $this->info("Email terkirim untuk kendaraan ID: {$service->kendaraan_id}");
        }

        $this->info('Selesai kirim semua overservice email.');
    }
}