<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AgingAr;
use App\Models\Setting;
use App\Mail\AgingArReminderMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendAgingReminder extends Command
{
    protected $signature = 'aging:reminder';

    protected $description = 'Mengirim reminder Aging AR';


    public function handle()
    {
        Log::info('=== AGING AR REMINDER START ===');


        $setting = Setting::first();


        if (!$setting || !$setting->email) {

            Log::warning('Aging AR gagal: email setting tidak ditemukan');

            return self::FAILURE;
        }



        // Konversi reminder
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



        $agingList = AgingAr::with([
            'member',
            'invoice'
        ])
        ->where('status', 'Belum Bayar')
        ->get();



        $emailTerkirim = 0;



        foreach ($agingList as $aging) {


            if (!$aging->jatuh_tempo) {
                continue;
            }



            $sisaHari = Carbon::today()->diffInDays(
                Carbon::parse($aging->jatuh_tempo),
                false
            );



            Log::info('Cek Aging AR', [

                'id' => $aging->id,

                'invoice' => $aging->invoice->invoice_no ?? '-',

                'customer' => $aging->member->nama_pelanggan ?? '-',

                'jatuh_tempo' => $aging->jatuh_tempo,

                'sisa_hari' => $sisaHari

            ]);





            // ==========================
            // REMINDER SEBELUM JATUH TEMPO
            // ==========================

            if (in_array($sisaHari, $hariReminder)) {


                try {


                    $email = $aging->member->email_pelanggan ?? null;


                    if (!$email) {

                        Log::warning('Email customer kosong', [
                            'aging_id' => $aging->id
                        ]);

                        continue;
                    }



                    Mail::to($email)
                        ->send(
                            new AgingArReminderMail(
                                $aging,
                                $sisaHari,
                                'reminder'
                            )
                        );



                    $emailTerkirim++;



                    Log::info('Email Aging AR reminder terkirim', [

                        'aging_id' => $aging->id,

                        'email' => $email,

                        'sisa_hari' => $sisaHari

                    ]);



                } catch (\Throwable $e) {


                    Log::error('Aging AR reminder gagal', [

                        'aging_id' => $aging->id,

                        'pesan' => $e->getMessage()

                    ]);

                }

            }





            // ==========================
            // TERLAMBAT H+1
            // ==========================

            if ($sisaHari == -1) {


                try {


                    $email = $aging->member->email_pelanggan ?? null;



                    if (!$email) {
                        continue;
                    }



                    Mail::to($email)
                        ->send(
                            new AgingArReminderMail(
                                $aging,
                                1,
                                'terlambat'
                            )
                        );



                    $emailTerkirim++;



                    Log::info('Email Aging AR terlambat terkirim', [

                        'aging_id' => $aging->id,

                        'email' => $email

                    ]);



                } catch (\Throwable $e) {


                    Log::error('Aging AR terlambat gagal', [

                        'aging_id' => $aging->id,

                        'pesan' => $e->getMessage()

                    ]);

                }

            }


        }



        Log::info('=== AGING AR REMINDER SELESAI ===', [

            'email_terkirim' => $emailTerkirim

        ]);



        return self::SUCCESS;
    }
}