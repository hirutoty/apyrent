<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\InvPenawaran;
use App\Models\Setting;
use App\Mail\PenawaranReminderMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReminderPenawaranCommand extends Command
{
    protected $signature = 'app:reminder-penawaran-command';

    protected $description = 'Reminder Penawaran Otomatis';


    public function handle()
    {

        Log::info('=== PENAWARAN REMINDER START ===', [
            'waktu'=>now()->toDateTimeString()
        ]);

        try {


            $setting = Setting::first();


            if (!$setting || !$setting->email) {

                Log::warning('Email setting belum diisi');

                return self::FAILURE;
            }



            $reminder = match ($setting->satuan_reminder) {

                'hari'   => $setting->batas_reminder,

                'minggu' => $setting->batas_reminder * 7,

                'bulan'  => $setting->batas_reminder * 30,

                'tahun'  => $setting->batas_reminder * 365,

                default => $setting->batas_reminder,

            };



            $hariReminder = array_map('intval', array_unique([

                $reminder,
                14,
                7,
                1,

            ]));



            $penawarans = InvPenawaran::all();



            Log::info('Jumlah penawaran ditemukan',[
                'jumlah'=>$penawarans->count()
            ]);



            $emailTerkirim = 0;



            foreach($penawarans as $penawaran){



                $tanggalBerakhir = Carbon::parse(
                    $penawaran->tanggal_penawaran
                );



                switch(strtolower($penawaran->dikatakan)){


                    case 'hari':

                        $tanggalBerakhir->addDays(
                            $penawaran->periode
                        );

                    break;



                    case 'minggu':

                        $tanggalBerakhir->addWeeks(
                            $penawaran->periode
                        );

                    break;



                    case 'bulan':

                        $tanggalBerakhir->addMonths(
                            $penawaran->periode
                        );

                    break;



                    case 'tahun':

                        $tanggalBerakhir->addYears(
                            $penawaran->periode
                        );

                    break;



                    default:

                        $tanggalBerakhir->addMonths(1);

                    break;

                }





                $sisaHari = (int) floor(
                    Carbon::today()->diffInDays(
                        $tanggalBerakhir,
                        false
                    )
                );




                Log::info('Cek penawaran',[

                    'id'=>$penawaran->id,

                    'nomor'=>$penawaran->no_penawaran,

                    'tanggal'=>$penawaran->tanggal_penawaran,

                    'berakhir'=>$tanggalBerakhir->format('Y-m-d'),

                    'sisa_hari'=>$sisaHari

                ]);







                // =====================
                // REMINDER
                // =====================

                if(
                    $sisaHari > 0 &&
                    in_array($sisaHari,$hariReminder)
                ){


                    Log::info('Kirim reminder penawaran',[

                        'id'=>$penawaran->id

                    ]);



                    // Admin

                    Mail::to($setting->email)
                        ->send(
                            new PenawaranReminderMail(
                                $penawaran,
                                $sisaHari,
                                'reminder'
                            )
                        );



                    // Customer

                    if(!empty($penawaran->email_person)){


                        Mail::to($penawaran->email_person)
                            ->send(
                                new PenawaranReminderMail(
                                    $penawaran,
                                    $sisaHari,
                                    'reminder'
                                )
                            );

                    }


                    $emailTerkirim++;

                }







                // =====================
                // TERLAMBAT
                // =====================

                if($sisaHari == -1){


                    Log::info('Kirim penawaran terlambat',[

                        'id'=>$penawaran->id

                    ]);



                    Mail::to($setting->email)
                        ->send(
                            new PenawaranReminderMail(
                                $penawaran,
                                1,
                                'terlambat'
                            )
                        );




                    if(!empty($penawaran->email_person)){


                        Mail::to($penawaran->email_person)
                            ->send(
                                new PenawaranReminderMail(
                                    $penawaran,
                                    1,
                                    'terlambat'
                                )
                            );

                    }


                    $emailTerkirim++;

                }


            }




            Log::info('=== PENAWARAN REMINDER SELESAI ===',[

                'email_terkirim'=>$emailTerkirim

            ]);



            return self::SUCCESS;



        } catch(\Throwable $e){



            Log::error('PENAWARAN REMINDER ERROR',[

                'pesan'=>$e->getMessage(),

                'file'=>$e->getFile(),

                'line'=>$e->getLine()

            ]);



            return self::FAILURE;

        }

    }
}