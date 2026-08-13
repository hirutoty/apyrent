<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ReminderService;
use App\Models\Setting;
use App\Mail\ReminderServiceMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CekJatuhTempoReminderService extends Command
{
    protected $signature = 'reminder-service:cek-jatuh-tempo';

    protected $description = 'Cek reminder service yang jatuh tempo, kirim email, dan auto-create ke Mobil Bermasalah';


    public function handle()
    {
        Log::info('=== REMINDER SERVICE START ===', [
            'waktu' => now()->toDateTimeString()
        ]);


        try {


            $setting = Setting::first();


            if (!$setting || !$setting->email) {

                Log::warning('Email setting tidak ditemukan');

                return self::FAILURE;
            }



            $reminders = ReminderService::with('kendaraan')
                ->where('status', '!=', 'selesai')
                ->get();



            Log::info('Jumlah reminder service ditemukan', [
                'jumlah'=>$reminders->count()
            ]);



            // hari reminder sebelum jatuh tempo

            $hariReminder = [
                7,
                3,
                1
            ];



            $count = 0;



            foreach ($reminders as $reminder) {


                if (!$reminder->tanggal_jatuh_tempo) {
                    continue;
                }



                $sisaHari = $reminder->sisaHari();



                $sudahJatuh = Carbon::today()
                    ->gte(
                        Carbon::parse(
                            $reminder->tanggal_jatuh_tempo
                        )
                    );




                Log::info('Cek reminder service', [

                    'id'=>$reminder->id,

                    'nama'=>$reminder->nama_reminder,

                    'kendaraan'=>$reminder->kendaraan->nopol ?? '-',

                    'tanggal'=>$reminder->tanggal_jatuh_tempo,

                    'sisa_hari'=>$sisaHari

                ]);




                // =============================
                // REMINDER SEBELUM JATUH TEMPO
                // =============================

                if (
                    !$sudahJatuh &&
                    in_array($sisaHari,$hariReminder)
                ) {


                    Mail::to($setting->email)
                        ->send(
                            new ReminderServiceMail(
                                $reminder,
                                $sisaHari,
                                'reminder'
                            )
                        );



                    Log::info('Email reminder service terkirim', [

                        'id'=>$reminder->id,

                        'hari'=>$sisaHari

                    ]);

                }





                // =============================
                // JATUH TEMPO
                // =============================

                if ($sudahJatuh) {



                    if ($reminder->status !== 'jatuh_tempo') {


                        $reminder->update([

                            'status'=>'jatuh_tempo'

                        ]);



                        Log::info('Status reminder berubah jatuh tempo', [

                            'id'=>$reminder->id

                        ]);

                    }





                    // email saat hari H

                    if ($sisaHari == 0) {


                        Mail::to($setting->email)
                            ->send(
                                new ReminderServiceMail(
                                    $reminder,
                                    $sisaHari,
                                    'jatuh_tempo'
                                )
                            );



                        Log::info('Email jatuh tempo terkirim', [

                            'id'=>$reminder->id

                        ]);

                    }






                    // auto masuk mobil bermasalah (legacy — dinonaktifkan di sistem baru)
                    // part limit sekarang ditangani oleh CheckServicePartLimit command

                    if (!$reminder->sudah_dibuat_masalah) {
                        // Tandai sudah diproses agar tidak re-trigger
                        $reminder->update(['sudah_dibuat_masalah' => true]);
                        $count++;

                        Log::info('Reminder jatuh tempo ditandai sudah diproses', [
                            'id'        => $reminder->id,
                            'kendaraan' => $reminder->kendaraan->nopol ?? '-',
                        ]);
                    }


                }

            }





            Log::info('=== REMINDER SERVICE SELESAI ===', [

                'diproses'=>$count

            ]);



            return self::SUCCESS;



        } catch(\Throwable $e) {



            Log::error('REMINDER SERVICE ERROR', [

                'pesan'=>$e->getMessage(),

                'file'=>$e->getFile(),

                'line'=>$e->getLine()

            ]);



            return self::FAILURE;

        }
    }
}