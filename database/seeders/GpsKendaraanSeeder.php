<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GpsKendaraan;

class GpsKendaraanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GpsKendaraan::create([
            'kendaraan_id' => 1,
            'gps_id' => 1,
            'type' => 'GT06N',
            'status_gps' => 'aktif',
            'tanggal_pasang' => now()->subMonths(5),
            'tanggal_habis' => now()->addMonths(7),
            'biaya_sewa' => 150000,
            'durasi_bulan' => 12,
            'status_sewa' => 'aktif',
        ]);

        GpsKendaraan::create([
            'kendaraan_id' => 2,
            'gps_id' => 2,
            'type' => 'TK103',
            'status_gps' => 'aktif',
            'tanggal_pasang' => now()->subMonths(10),
            'tanggal_habis' => now()->addDays(5),
            'biaya_sewa' => 120000,
            'durasi_bulan' => 12,
            'status_sewa' => 'habis',
        ]);

        GpsKendaraan::create([
            'kendaraan_id' => 3,
            'gps_id' => 3,
            'type' => 'Concox JM01',
            'status_gps' => 'nonaktif',
            'tanggal_pasang' => now()->subYear(),
            'tanggal_habis' => now()->subDays(2),
            'biaya_sewa' => 100000,
            'durasi_bulan' => 12,
            'status_sewa' => 'habis',
        ]);

        GpsKendaraan::create([
            'kendaraan_id' => 1,
            'gps_id' => 4,
            'type' => 'GT02A',
            'status_gps' => 'nonaktif',
            'tanggal_pasang' => now()->subMonths(2),
            'tanggal_habis' => now()->addMonths(10),
            'biaya_sewa' => 175000,
            'durasi_bulan' => 12,
            'status_sewa' => 'aktif',
        ]);
    }
}