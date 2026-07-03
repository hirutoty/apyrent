<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PajakKendaraan;

class PajakKendaraanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PajakKendaraan::create([
            'kendaraan_id' => 1,
            'jenis_pajak' => 'Pajak Tahunan',
            'nominal' => 3500000,
            'jatuh_tempo' => now()->addDays(5),
            'tanggal_bayar' => null,
            'status' => 'belum_bayar',
            'keterangan' => 'Pajak hampir jatuh tempo',
            'bukti' => null,
        ]);

        PajakKendaraan::create([
            'kendaraan_id' => 2,
            'jenis_pajak' => 'STNK',
            'nominal' => 2200000,
            'jatuh_tempo' => now()->addDays(12),
            'tanggal_bayar' => null,
            'status' => 'belum_bayar',
            'keterangan' => 'Segera lakukan pembayaran',
            'bukti' => null,
        ]);

        PajakKendaraan::create([
            'kendaraan_id' => 3,
            'jenis_pajak' => 'Pajak 5 Tahunan',
            'nominal' => 5400000,
            'jatuh_tempo' => now()->subDays(2),
            'tanggal_bayar' => null,
            'status' => 'belum_bayar',
            'keterangan' => 'Sudah melewati jatuh tempo',
            'bukti' => null,
        ]);

        PajakKendaraan::create([
            'kendaraan_id' => 2,
            'jenis_pajak' => 'Pajak Tahunan',
            'nominal' => 3100000,
            'jatuh_tempo' => now()->addMonths(2),
            'tanggal_bayar' => now(),
            'status' => 'sudah_bayar',
            'keterangan' => 'Pembayaran berhasil',
            'bukti' => 'bukti-pajak/bukti1.jpg',
        ]);

        PajakKendaraan::create([
            'kendaraan_id' => 1,
            'jenis_pajak' => 'STNK',
            'nominal' => 1800000,
            'jatuh_tempo' => now()->addDays(3),
            'tanggal_bayar' => null,
            'status' => 'belum_bayar',
            'keterangan' => 'Perlu segera diperpanjang',
            'bukti' => null,
        ]);
    }
}   