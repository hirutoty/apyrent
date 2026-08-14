<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ServicePart;
use App\Models\ReminderService;
use App\Models\Setting;
use App\Mail\ReminderServiceMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckServicePartLimit extends Command
{
    protected $signature   = 'service:check-part-limit';
    protected $description = 'Cek part kendaraan yang sudah melewati tanggal limit, update status, buat reminder, kirim email ringkasan.';

    public function handle()
    {
        Log::info('=== CHECK SERVICE PART LIMIT START ===', [
            'waktu' => now()->toDateTimeString(),
        ]);

        try {
            $setting = Setting::first();

            if (!$setting || !$setting->email) {
                Log::warning('CheckServicePartLimit: email setting tidak ditemukan, abort.');
                return self::FAILURE;
            }

            // Ambil semua part yang masih Terpasang dan sudah melewati tanggal limit
            $partsLimit = ServicePart::with(['kendaraan', 'category'])
                ->where('status', 'Terpasang')
                ->whereNotNull('tanggal_limit')
                ->whereDate('tanggal_limit', '<=', Carbon::today())
                ->get();

            Log::info('Part yang melewati limit ditemukan', ['jumlah' => $partsLimit->count()]);

            $newReminderParts = []; // kumpulkan untuk email ringkasan

            foreach ($partsLimit as $part) {
                // Update status part → Limit
                $part->update(['status' => 'Limit']);

                Log::info('Part diupdate ke Limit', [
                    'part_id'    => $part->id,
                    'nama_part'  => $part->nama_part,
                    'kendaraan'  => $part->kendaraan?->nopol ?? '-',
                    'tgl_limit'  => $part->tanggal_limit,
                ]);

                // Cek apakah sudah ada reminder aktif untuk part ini
                $reminderAktif = ReminderService::where('service_part_id', $part->id)
                    ->whereIn('status', ['aktif', 'jatuh_tempo'])
                    ->exists();

                if (!$reminderAktif) {
                    // Buat reminder baru
                    $namaReminder = 'Limit: ' . $part->nama_part
                        . ' — ' . ($part->kendaraan?->merk ?? '')
                        . ' ' . ($part->kendaraan?->nopol ?? '');

                    $reminder = ReminderService::create([
                        'kendaraan_id'         => $part->kendaraan_id,
                        'service_part_id'      => $part->id,
                        'nama_reminder'        => $namaReminder,
                        'tanggal_mulai'        => $part->tgl_pasang,
                        'interval_nilai'       => $part->interval_nilai,
                        'interval_satuan'      => $part->interval_satuan,
                        'tanggal_jatuh_tempo'  => $part->tanggal_limit,
                        'keterangan'           => 'Auto-generated: part melewati interval ' . $part->interval_nilai . ' ' . $part->interval_satuan,
                        'biaya'                => $part->biaya > 0 ? $part->biaya : null,
                        'status'               => 'jatuh_tempo',
                        'sudah_dibuat_masalah' => false,
                    ]);

                    $newReminderParts[] = [
                        'part'     => $part,
                        'reminder' => $reminder,
                    ];

                    Log::info('Reminder baru dibuat untuk part limit', [
                        'reminder_id' => $reminder->id,
                        'part_id'     => $part->id,
                    ]);
                } else {
                    Log::info('Skip: reminder aktif sudah ada untuk part ini', [
                        'part_id' => $part->id,
                    ]);
                }
            }

            // Kirim 1 email ringkasan jika ada part baru yang limit
            if (!empty($newReminderParts)) {
                try {
                    Mail::to($setting->email)
                        ->send(new ReminderServiceMail(null, 0, 'part_limit', $newReminderParts));

                    Log::info('Email ringkasan part limit terkirim', [
                        'jumlah_part' => count($newReminderParts),
                        'ke'          => $setting->email,
                    ]);
                } catch (\Throwable $mailErr) {
                    Log::warning('Gagal kirim email ringkasan part limit', [
                        'error' => $mailErr->getMessage(),
                    ]);
                }
            }

            Log::info('=== CHECK SERVICE PART LIMIT SELESAI ===', [
                'diproses'         => $partsLimit->count(),
                'reminder_dibuat'  => count($newReminderParts),
            ]);

            return self::SUCCESS;

        } catch (\Throwable $e) {
            Log::error('CHECK SERVICE PART LIMIT ERROR', [
                'pesan' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ]);
            return self::FAILURE;
        }
    }
}
