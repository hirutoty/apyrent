<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kir;
use App\Models\Setting;
use App\Mail\KirReminderMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReminderKirCommand extends Command
{
    protected $signature = 'kir:reminder';

    protected $description = 'Kirim reminder KIR kendaraan';


    public function handle()
    {
        Log::info('=== KIR REMINDER START ===');


        $setting = Setting::first();


        if (!$setting || !$setting->email) {

            Log::warning('KIR reminder gagal: email setting tidak ditemukan');

            return self::FAILURE;
        }



        // Konversi batas reminder
        $reminder = match ($setting->satuan_reminder) {

            'hari'   => (int) $setting->batas_reminder,

            'minggu' => (int) $setting->batas_reminder * 7,

            'bulan'  => (int) $setting->batas_reminder * 30,

            'tahun'  => (int) $setting->batas_reminder * 365,

            default  => (int) $setting->batas_reminder,

        };



        // Pola reminder
        $hariReminder = array_unique([
            $reminder,
            14,
            7,
            3,
            1
        ]);



        $kirList = Kir::with('kendaraan')->get();


        $emailTerkirim = 0;



        foreach ($kirList as $kir) {


            if (!$kir->masa_berlaku) {
                continue;
            }



            $sisaHari = Carbon::today()->diffInDays(
                Carbon::parse($kir->masa_berlaku),
                false
            );



            Log::info('Cek KIR', [

                'id' => $kir->id,

                'nopol' => $kir->kendaraan->nopol ?? '-',

                'masa_berlaku' => $kir->masa_berlaku,

                'sisa_hari' => $sisaHari

            ]);




            // Reminder sebelum jatuh tempo
            if (in_array($sisaHari, $hariReminder)) {


                try {


                    Mail::to($setting->email)
                        ->send(
                            new KirReminderMail(
                                $kir,
                                $sisaHari,
                                'reminder'
                            )
                        );



                    $emailTerkirim++;



                    Log::info('Email reminder KIR terkirim', [

                        'kir_id' => $kir->id,

                        'sisa_hari' => $sisaHari

                    ]);



                } catch (\Throwable $e) {


                    Log::error('KIR reminder gagal', [

                        'kir_id' => $kir->id,

                        'pesan' => $e->getMessage()

                    ]);

                }

            }




            // Terlambat H+1
            if ($sisaHari == -1) {


                try {


                    Mail::to($setting->email)
                        ->send(
                            new KirReminderMail(
                                $kir,
                                1,
                                'terlambat'
                            )
                        );



                    $emailTerkirim++;



                    Log::info('Email KIR terlambat terkirim', [

                        'kir_id' => $kir->id

                    ]);



                } catch (\Throwable $e) {


                    Log::error('KIR terlambat gagal', [

                        'kir_id' => $kir->id,

                        'pesan' => $e->getMessage()

                    ]);

                }

            }

        }



        Log::info('=== KIR REMINDER SELESAI ===', [

            'email_terkirim' => $emailTerkirim

        ]);



        return self::SUCCESS;
    }
}