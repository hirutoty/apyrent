<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StnkHistory;
use App\Models\Stnk;
use App\Models\Kendaraan;
use Carbon\Carbon;

class StnkHistorySeeder extends Seeder
{
    public function run(): void
    {
        $stnks = Stnk::with('kendaraan')->get();

        if ($stnks->isEmpty()) {
            $this->command->error('Tidak ada data STNK. Jalankan StnkSeeder terlebih dahulu.');
            return;
        }

        $year = 2026;
        $data = [];

        // Generate 120 history tersebar Jan-Des 2026
        // 10 history per bulan
        for ($month = 1; $month <= 12; $month++) {
            $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

            for ($j = 0; $j < 10; $j++) {
                $day = rand(1, $daysInMonth);
                $diperpanjangPada = Carbon::create($year, $month, $day)
                    ->setTime(rand(8, 16), rand(0, 59), rand(0, 59));

                // Ambil random STNK dari pool
                $stnk = $stnks->random();
                $kendaraan = $stnk->kendaraan;

                // Masa berlaku baru: +5 tahun dari tanggal perpanjangan
                $masaBerlakuBaru = $diperpanjangPada->copy()->addYears(5)->toDateString();

                $data[] = [
                    'stnk_id'          => $stnk->id,
                    'kendaraan_id'     => $kendaraan->id,
                    'nopol'            => $stnk->nopol ?? 'B-' . rand(1000, 9999) . '-XXX',
                    'merk'             => $stnk->merk ?? $kendaraan->merk ?? 'Unknown',
                    'nama_pemilik'     => $stnk->nama_pemilik ?? 'PT Apyrent Indonesia',
                    'jenis_model'      => $stnk->jenis_model ?? $kendaraan->model ?? 'Sedan',
                    'masa_berlaku'     => $masaBerlakuBaru,
                    'biaya'            => rand(200000, 2000000),
                    'bukti'            => rand(0, 1) ? 'stnk_history_' . time() . '_' . $j . '.pdf' : null,
                    'diperpanjang_pada'=> $diperpanjangPada,
                    'created_at'       => $diperpanjangPada,
                    'updated_at'       => $diperpanjangPada,
                ];
            }
        }

        // Batch insert
        foreach ($data as $item) {
            StnkHistory::create($item);
        }

        $this->command->info('StnkHistorySeeder selesai: 120 history dibuat (10 per bulan Jan-Des 2026).');
    }
}
