<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ServicePart;
use App\Models\ServiceHistory;
use App\Models\ReminderService;
use App\Models\Setting;
use App\Mail\ReminderServiceMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckServicePartLimit extends Command
{
    protected $signature   = 'service:check-part-limit';
    protected $description = 'Cek part kendaraan yang sudah melewati tanggal limit, update status part & service history, buat reminder, kirim email ringkasan.';

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

            // ──────────────────────────────────────────────────────────────────
            // STEP 1: Ambil semua part Terpasang yang sudah melewati tanggal limit
            // ──────────────────────────────────────────────────────────────────
            $partsLimit = ServicePart::with(['kendaraan', 'category'])
                ->where('status', 'Terpasang')
                ->whereNotNull('tanggal_limit')
                ->whereDate('tanggal_limit', '<=', Carbon::today())
                ->get();

            Log::info('Part yang melewati limit ditemukan', ['jumlah' => $partsLimit->count()]);

            $newReminderParts = [];

            foreach ($partsLimit as $part) {
                // Update status part → Limit
                $part->update(['status' => 'Limit']);

                Log::info('Part diupdate ke Limit', [
                    'part_id'    => $part->id,
                    'nama_part'  => $part->nama_part,
                    'kendaraan'  => optional($part->kendaraan)->nopol ?? '-',
                    'tgl_limit'  => $part->tanggal_limit,
                ]);

                // Update service history induk → limit
                if ($part->service_history_id) {
                    // Proses menang atas Limit — jangan override ke limit jika masih ada part Proses
                    $adaProses = ServicePart::where('service_history_id', $part->service_history_id)
                        ->where('status', 'Proses')
                        ->exists();

                    if (!$adaProses) {
                        $updated = ServiceHistory::where('id', $part->service_history_id)
                            ->whereIn('status', ['proses', 'selesai'])
                            ->update(['status' => 'limit']);

                        if ($updated) {
                            Log::info('Service history diupdate ke limit', [
                                'service_history_id' => $part->service_history_id,
                                'part_id'            => $part->id,
                            ]);
                        }
                    } else {
                        Log::info('Skip update ke limit: masih ada part Proses di service history', [
                            'service_history_id' => $part->service_history_id,
                            'part_id'            => $part->id,
                        ]);
                    }
                }

                // Cek apakah sudah ada reminder aktif untuk part ini
                $reminderAktif = ReminderService::where('service_part_id', $part->id)
                    ->whereIn('status', ['aktif', 'jatuh_tempo'])
                    ->exists();

                if (!$reminderAktif) {
                    $namaReminder = 'Limit: ' . $part->nama_part
                        . ' — ' . (optional($part->kendaraan)->merk ?? '')
                        . ' ' . (optional($part->kendaraan)->nopol ?? '');

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

            // ──────────────────────────────────────────────────────────────────
            // STEP 2: Revert service history yang statusnya `limit` tapi
            //         semua parts di dalamnya sudah Diganti (tidak ada yang Limit lagi)
            // ──────────────────────────────────────────────────────────────────
            $revertCount = 0;

            ServiceHistory::where('status', 'limit')
                ->with('parts')
                ->each(function ($sh) use (&$revertCount) {
                    $masihAdaLimit = $sh->parts()
                        ->where('status', 'Limit')
                        ->exists();

                    if (!$masihAdaLimit) {
                        $sh->update(['status' => 'selesai']);
                        $revertCount++;

                        Log::info('Service history direvert ke selesai (tidak ada part Limit)', [
                            'service_history_id' => $sh->id,
                            'kendaraan_id'       => $sh->kendaraan_id,
                        ]);
                    }
                });

            Log::info('Revert service history selesai', ['direvert' => $revertCount]);

            // ──────────────────────────────────────────────────────────────────
            // STEP 3: Kirim email ringkasan jika ada part baru yang limit
            // ──────────────────────────────────────────────────────────────────
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
                'sh_direvert'      => $revertCount,
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
