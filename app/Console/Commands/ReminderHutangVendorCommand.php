<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\HutangVendor;
use App\Models\Setting;
use App\Mail\ReminderHutangVendorMail;
use Carbon\Carbon;

class ReminderHutangVendorCommand extends Command
{
    protected $signature = 'hutang:reminder';

    protected $description = 'Kirim reminder hutang vendor';


    public function handle()
    {
        Log::info('=== HUTANG VENDOR REMINDER START ===');


        $setting = Setting::first();


        if (!$setting || !$setting->email) {

            Log::warning('Email setting hutang vendor tidak ditemukan');

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



        // pola reminder
        $hariReminder = array_unique([
            $reminder,
            14,
            7,
            3,
            1
        ]);



        $hutangs = HutangVendor::where('status', 'belum_lunas')
            ->get();



        $emailTerkirim = 0;



        foreach ($hutangs as $hutang) {


            if (!$hutang->jatuh_tempo) {
                continue;
            }



            $sisaHari = Carbon::today()->diffInDays(
                Carbon::parse($hutang->jatuh_tempo),
                false
            );



            Log::info('Cek hutang vendor', [

                'id' => $hutang->id,

                'vendor' => $hutang->nama_vendor ?? '-',

                'jatuh_tempo' => $hutang->jatuh_tempo,

                'sisa_hari' => $sisaHari

            ]);




            // otomatis terlambat
            if ($sisaHari < 0) {


                if ($hutang->status != 'terlambat') {

                    $hutang->update([
                        'status' => 'terlambat'
                    ]);

                }


            }




            // reminder sebelum jatuh tempo
            if (in_array($sisaHari, $hariReminder)) {


                try {


                    Mail::to($setting->email)
                        ->send(
                            new ReminderHutangVendorMail(
                                $hutang,
                                $sisaHari,
                                'reminder'
                            )
                        );



                    $hutang->update([
                        'last_reminder_at' => now()
                    ]);



                    $emailTerkirim++;



                    Log::info('Email reminder hutang terkirim', [

                        'hutang_id' => $hutang->id,

                        'sisa_hari' => $sisaHari

                    ]);



                } catch (\Throwable $e) {


                    Log::error('Gagal kirim reminder hutang', [

                        'hutang_id' => $hutang->id,

                        'pesan' => $e->getMessage()

                    ]);

                }

            }




            // terlambat H+1
            if ($sisaHari == -1) {


                try {


                    Mail::to($setting->email)
                        ->send(
                            new ReminderHutangVendorMail(
                                $hutang,
                                1,
                                'terlambat'
                            )
                        );



                    $hutang->update([
                        'last_reminder_at' => now()
                    ]);



                    $emailTerkirim++;



                    Log::info('Email hutang terlambat terkirim', [

                        'hutang_id' => $hutang->id

                    ]);



                } catch (\Throwable $e) {


                    Log::error('Gagal kirim email terlambat hutang', [

                        'hutang_id' => $hutang->id,

                        'pesan' => $e->getMessage()

                    ]);

                }

            }


        }



        Log::info('=== HUTANG VENDOR REMINDER SELESAI ===', [

            'email_terkirim' => $emailTerkirim

        ]);



        return self::SUCCESS;
    }
}