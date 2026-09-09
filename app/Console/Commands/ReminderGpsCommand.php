<?php

namespace App\Console\Commands;

use App\Mail\GpsReminderMail;
use App\Models\GpsKendaraan;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ReminderGpsCommand extends Command
{
    protected $signature = 'gps:reminder';

    protected $description = 'Kirim reminder GPS kendaraan';

    public function handle()
    {
        Log::info('=== GPS REMINDER START ===');


        $setting = Setting::first();


        if (!$setting || !$setting->email) {

            Log::warning('GPS reminder gagal: email setting tidak ditemukan');

            return self::FAILURE;
        }


        $reminder = match ($setting->satuan_reminder) {

            'hari'   => (int) $setting->batas_reminder,

            'minggu' => (int) $setting->batas_reminder * 7,

            'bulan'  => (int) $setting->batas_reminder * 30,

            'tahun'  => (int) $setting->batas_reminder * 365,

            default  => (int) $setting->batas_reminder,
        };


        // tanggal reminder
        $hariReminder = array_unique([
            $reminder,
            14,
            7,
            3,
            1,
        ]);


        $gpsList = GpsKendaraan::with('kendaraan')->get();


        $emailTerkirim = 0;


        foreach ($gpsList as $gps) {


            if (!$gps->tanggal_habis) {
                continue;
            }


            $sisaHari = Carbon::today()->diffInDays(
                Carbon::parse($gps->tanggal_habis),
                false
            );


            Log::info('Cek GPS', [
                'id' => $gps->id,
                'nopol' => $gps->kendaraan->nopol ?? '-',
                'sisa_hari' => $sisaHari
            ]);



            // otomatis expired
            if ($sisaHari < 0 && $gps->status_sewa != 'expired') {


                $gps->update([
                    'status_sewa' => 'expired'
                ]);


                Log::info('GPS expired otomatis', [
                    'id' => $gps->id
                ]);
            }



            // reminder sebelum habis
            if (in_array($sisaHari, $hariReminder)) {


                try {


                    Mail::to($setting->email)
                        ->send(
                            new GpsReminderMail(
                                $gps,
                                $sisaHari,
                                'reminder'
                            )
                        );


                    $emailTerkirim++;


                    Log::info('Email GPS reminder terkirim', [
                        'gps_id' => $gps->id,
                        'sisa_hari' => $sisaHari
                    ]);


                } catch (\Throwable $e) {


                    Log::error('GPS reminder gagal', [
                        'gps_id' => $gps->id,
                        'pesan' => $e->getMessage()
                    ]);

                }

            }



            // terlambat H+1
            if ($sisaHari == -1) {


                try {


                    Mail::to($setting->email)
                        ->send(
                            new GpsReminderMail(
                                $gps,
                                1,
                                'terlambat'
                            )
                        );


                    $emailTerkirim++;


                    Log::info('Email GPS terlambat terkirim', [
                        'gps_id' => $gps->id
                    ]);



                } catch (\Throwable $e) {


                    Log::error('GPS terlambat gagal', [
                        'gps_id' => $gps->id,
                        'pesan' => $e->getMessage()
                    ]);

                }

            }

        }


        Log::info('=== GPS REMINDER SELESAI ===', [
            'email_terkirim' => $emailTerkirim
        ]);


        return self::SUCCESS;
    }
}