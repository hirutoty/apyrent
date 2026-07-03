<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AsuransiKendaraan;

class AsuransiKendaraanSeeder extends Seeder
{
    public function run(): void
    {
        AsuransiKendaraan::create([
            'kendaraan_id' => 1,
            'asuransi_id' => 1,
            'jenis_asuransi_id' => 1,
            'status_kendaraan' => 'aktif',
            'tgl_mulai' => now(),
            'tgl_berakhir' => now()->addYear(),
            'durasi_bulan' => 12,
            'biaya' => 5000000,
        ]);

        AsuransiKendaraan::create([
            'kendaraan_id' => 2,
            'asuransi_id' => 2,
            'jenis_asuransi_id' => 2,
            'status_kendaraan' => 'aktif',
            'tgl_mulai' => now(),
            'tgl_berakhir' => now()->addMonths(6),
            'durasi_bulan' => 6,
            'biaya' => 2500000,
        ]);

        AsuransiKendaraan::create([
            'kendaraan_id' => 1,
            'asuransi_id' => 3,
            'jenis_asuransi_id' => 3,
            'status_kendaraan' => 'expired',
            'tgl_mulai' => now()->subMonths(8),
            'tgl_berakhir' => now()->subDays(5),
            'durasi_bulan' => 8,
            'biaya' => 3500000,
        ]);
    }
}