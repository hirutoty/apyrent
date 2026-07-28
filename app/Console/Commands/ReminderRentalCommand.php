<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Rental;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\RentalReminderMail;

class ReminderRentalCommand extends Command
{
    protected $signature = 'app:reminder-rental-command';

    protected $description = 'Reminder rental otomatis';


    public function handle()
    {

        Log::info('=== RENTAL REMINDER START ===', [
            'waktu' => now()->toDateTimeString()
        ]);


        try {


            $setting = Setting::first();


            if (!$setting) {

                Log::warning('Setting tidak ditemukan');

                return self::FAILURE;
            }




            $reminder = match ($setting->satuan_reminder) {

                'hari'   => $setting->batas_reminder,

                'minggu' => $setting->batas_reminder * 7,

                'bulan'  => $setting->batas_reminder * 30,

                'tahun'  => $setting->batas_reminder * 365,

                default  => $setting->batas_reminder,

            };



            $hariReminder = array_map('intval', array_unique([

                $reminder,
                7,
                1

            ]));



            $rentals = Rental::with([
                    'kendaraan',
                    'member'
                ])
                ->whereNotNull('tanggal_selesai')
                ->where('status','aktif')
                ->get();



            Log::info('Jumlah rental aktif ditemukan', [

                'jumlah'=>$rentals->count()

            ]);



            $emailTerkirim = 0;



            foreach($rentals as $rental){



                if($rental->status !== 'aktif'){
                    continue;
                }



                $sisaHari = (int) floor(

                    Carbon::today()->diffInDays(

                        Carbon::parse(
                            $rental->tanggal_selesai
                        ),

                        false

                    )

                );




                Log::info('Cek rental',[

                    'id'=>$rental->id,

                    'kendaraan'=>$rental->kendaraan->nopol ?? '-',

                    'member'=>$rental->member->nama ?? '-',

                    'tanggal_selesai'=>$rental->tanggal_selesai,

                    'sisa_hari'=>$sisaHari

                ]);







                // ==========================
                // AUTO SELESAI
                // ==========================

                if(
                    $sisaHari < 0 &&
                    $rental->status !== 'selesai'
                ){


                    $rental->update([

                        'status'=>'selesai'

                    ]);



                    Log::info('Rental otomatis selesai',[

                        'id'=>$rental->id

                    ]);

                }







                // ==========================
                // REMINDER
                // ==========================

                if(
                    $sisaHari > 0 &&
                    in_array($sisaHari,$hariReminder)
                ){



                    Log::info('Kirim reminder rental',[

                        'id'=>$rental->id,

                        'hari'=>$sisaHari

                    ]);




                    // Admin

                    if(!empty($setting->email)){


                        Mail::to($setting->email)
                            ->send(
                                new RentalReminderMail(
                                    $rental,
                                    $sisaHari,
                                    'reminder'
                                )
                            );


                        $emailTerkirim++;

                    }







                    // Member

                    if(
                        !empty(
                            $rental->member?->email_pelanggan
                        )
                    ){


                        Mail::to(
                            $rental->member->email_pelanggan
                        )
                        ->send(
                            new RentalReminderMail(
                                $rental,
                                $sisaHari,
                                'reminder'
                            )
                        );


                        $emailTerkirim++;

                    }


                }








                // ==========================
                // TERLAMBAT H+1
                // ==========================

                if($sisaHari == -1){



                    Log::info('Kirim rental terlambat',[

                        'id'=>$rental->id

                    ]);




                    // Admin

                    if(!empty($setting->email)){


                        Mail::to($setting->email)
                            ->send(
                                new RentalReminderMail(
                                    $rental,
                                    1,
                                    'terlambat'
                                )
                            );


                        $emailTerkirim++;

                    }






                    // Member

                    if(
                        !empty(
                            $rental->member?->email_pelanggan
                        )
                    ){


                        Mail::to(
                            $rental->member->email_pelanggan
                        )
                        ->send(
                            new RentalReminderMail(
                                $rental,
                                1,
                                'terlambat'
                            )
                        );


                        $emailTerkirim++;

                    }

                }

            }




            Log::info('=== RENTAL REMINDER SELESAI ===',[

                'email_terkirim'=>$emailTerkirim

            ]);



            return self::SUCCESS;



        } catch(\Throwable $e){



            Log::error('RENTAL REMINDER ERROR',[

                'pesan'=>$e->getMessage(),

                'file'=>$e->getFile(),

                'line'=>$e->getLine()

            ]);



            return self::FAILURE;

        }

    }
}