<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PajakKendaraan;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Mail\PajakReminderMail;

class ReminderPajakCommand extends Command
{
    protected $signature = 'pajak:reminder';

    protected $description = 'Kirim reminder pajak kendaraan';


    public function handle()
    {
        Log::info('=== PAJAK REMINDER START ===');


        $setting = Setting::first();


        if (!$setting || !$setting->email) {

            Log::warning('Pajak reminder gagal: email setting tidak ditemukan');

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
        $hariReminder = [
            $reminder,
            14,
            7,
            3,
            1
        ];



        $data = PajakKendaraan::with('kendaraan')
            ->where('status', '!=', 'sudah_bayar')
            ->get();



        $emailTerkirim = 0;



        foreach ($data as $pajak) {


            if (!$pajak->jatuh_tempo) {
                continue;
            }



            $sisaHari = Carbon::today()->diffInDays(
                Carbon::parse($pajak->jatuh_tempo),
                false
            );



            Log::info('Cek Pajak', [

                'id' => $pajak->id,

                'nopol' => $pajak->kendaraan->nopol ?? '-',

                'jatuh_tempo' => $pajak->jatuh_tempo,

                'sisa_hari' => $sisaHari,

                'reminder' => $reminder

            ]);




            // Reminder sebelum jatuh tempo
            if (in_array($sisaHari, $hariReminder)) {


                try {


                    Mail::to($setting->email)
                        ->send(
                            new PajakReminderMail(
                                $pajak,
                                $sisaHari,
                                'reminder'
                            )
                        );


                    $emailTerkirim++;


                    Log::info('Email pajak reminder terkirim', [

                        'pajak_id' => $pajak->id,

                        'sisa_hari' => $sisaHari

                    ]);



                } catch (\Throwable $e) {


                    Log::error('Pajak reminder gagal', [

                        'pajak_id' => $pajak->id,

                        'pesan' => $e->getMessage()

                    ]);

                }

            }




            // Terlambat H+1
            if ($sisaHari == -1) {


                try {


                    Mail::to($setting->email)
                        ->send(
                            new PajakReminderMail(
                                $pajak,
                                1,
                                'terlambat'
                            )
                        );



                    $emailTerkirim++;



                    Log::info('Email pajak terlambat terkirim', [

                        'pajak_id' => $pajak->id

                    ]);



                } catch (\Throwable $e) {


                    Log::error('Pajak terlambat gagal', [

                        'pajak_id' => $pajak->id,

                        'pesan' => $e->getMessage()

                    ]);

                }

            }

        }



        Log::info('=== PAJAK REMINDER SELESAI ===', [

            'jumlah_data' => $data->count(),

            'email_terkirim' => $emailTerkirim

        ]);



        return self::SUCCESS;
    }
}