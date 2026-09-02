<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stnk;
use App\Models\Kendaraan;
use Carbon\Carbon;

class StnkSeeder extends Seeder
{
    public function run(): void
    {
        $kendaraans = Kendaraan::all();

        if ($kendaraans->isEmpty()) {
            $this->command->error('Tidak ada data kendaraan. Jalankan KendaraanSeeder terlebih dahulu.');
            return;
        }

        foreach ($kendaraans as $kendaraan) {
            // Masa berlaku STNK: 5 tahun dari masa_berlaku terakhir (atau random di 2025-2030)
            $masaBerlaku = Carbon::create(rand(2025, 2030), rand(1, 12), rand(1, 28));
            
            Stnk::create([
                'kendaraan_id' => $kendaraan->id,
                'nopol'        => $kendaraan->no_polisi ?? 'B-' . rand(1000, 9999) . '-XXX',
                'merk'         => $kendaraan->merk ?? 'Unknown',
                'nama_pemilik' => 'PT Apyrent Indonesia',
                'jenis_model'  => $kendaraan->model ?? 'Sedan',
                'masa_berlaku' => $masaBerlaku->toDateString(),
                'biaya'        => rand(200000, 500000),
                'bukti'        => 'stnk_' . $kendaraan->id . '.pdf',
            ]);
        }

        $this->command->info('StnkSeeder selesai: ' . $kendaraans->count() . ' STNK dibuat (1 per kendaraan).');
    }
}
