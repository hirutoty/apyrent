<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\InvPenawaran;
use App\Models\Setting;
use App\Mail\PenawaranReminderMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class ReminderPenawaranCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reminder-penawaran-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reminder Penawaran Otomatis';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Command Penawaran dijalankan');

        $setting = Setting::first();

        if (!$setting || !$setting->email) {
            $this->error('Email setting belum diisi');
            return;
        }

        // Convert setting reminder
        $reminder = match ($setting->satuan_reminder) {
            'hari'   => $setting->batas_reminder,
            'minggu' => $setting->batas_reminder * 7,
            'bulan'  => $setting->batas_reminder * 30,
            'tahun'  => $setting->batas_reminder * 365,
            default  => $setting->batas_reminder,
        };

        // Pola reminder sama seperti command lain
        $hariReminder = array_map('intval', array_unique([
            $reminder,
            14,
            7,
            1,
        ]));

        $penawarans = InvPenawaran::all();

        foreach ($penawarans as $penawaran) {

            // Hitung tanggal berakhir berdasarkan periode
            $tanggalBerakhir = Carbon::parse($penawaran->tanggal_penawaran);

            switch (strtolower($penawaran->dikatakan)) {
                case 'hari':
                    $tanggalBerakhir->addDays($penawaran->periode);
                    break;

                case 'minggu':
                    $tanggalBerakhir->addWeeks($penawaran->periode);
                    break;

                case 'bulan':
                    $tanggalBerakhir->addMonths($penawaran->periode);
                    break;

                case 'tahun':
                    $tanggalBerakhir->addYears($penawaran->periode);
                    break;

                default:
                    $tanggalBerakhir->addMonths(1);
                    break;
            }

            $sisaHari = (int) floor(
                Carbon::today()->diffInDays($tanggalBerakhir, false)
            );

            $this->info("Tanggal Penawaran : {$penawaran->tanggal_penawaran}");
            $this->info("Berlaku Sampai    : " . $tanggalBerakhir->format('Y-m-d'));
            $this->info("Sisa Hari         : {$sisaHari}");

            $this->info("Penawaran : {$penawaran->no_penawaran}");
            $this->info("Sisa Hari : {$sisaHari}");


            // Reminder
        
            if ($sisaHari > 0 && in_array($sisaHari, $hariReminder)) {

                $this->info('Kirim reminder penawaran');

                // Email Admin
                Mail::to($setting->email)->send(
                    new PenawaranReminderMail(
                        $penawaran,
                        $sisaHari,
                        'reminder'
                    )
                );

                // Email Customer
                if (!empty($penawaran->email_person)) {
                    Mail::to($penawaran->email_person)->send(
                        new PenawaranReminderMail(
                            $penawaran,
                            $sisaHari,
                            'reminder'
                        )
                    );
                }
            }

            // Terlambat
            if ($sisaHari == -1) {

                $this->info('Kirim penawaran terlambat');

                // Email Admin
                Mail::to($setting->email)->send(
                    new PenawaranReminderMail(
                        $penawaran,
                        1,
                        'terlambat'
                    )
                );

                // Email Customer
                if (!empty($penawaran->email_person)) {
                    Mail::to($penawaran->email_person)->send(
                        new PenawaranReminderMail(
                            $penawaran,
                            1,
                            'terlambat'
                        )
                    );
                }
            }
        }

        $this->info('Selesai reminder penawaran');
    }
}
