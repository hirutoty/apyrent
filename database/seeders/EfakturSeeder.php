<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Efaktur;
use Carbon\Carbon;

class EfakturSeeder extends Seeder
{
    public function run(): void
    {
        Efaktur::create([
            'nomor_faktur' => '010.000-26.000001',
            'tanggal_faktur' => Carbon::now()->subDays(10),
            'tipe' => 'Keluaran',
            'npwp_lawan' => '01.234.567.8-901.000',
            'nama_lawan' => 'PT Rental Maju Jaya',
            'dpp' => 5000000,
            'ppn' => 550000,
            'ppnbm' => 0,
            'status' => 'terbit',
            'file_faktur' => null,
        ]);

        Efaktur::create([
            'nomor_faktur' => '010.000-26.000002',
            'tanggal_faktur' => Carbon::now()->subDays(5),
            'tipe' => 'Masukan',
            'npwp_lawan' => '09.876.543.2-109.000',
            'nama_lawan' => 'PT Supplier Sparepart',
            'dpp' => 3000000,
            'ppn' => 330000,
            'ppnbm' => 0,
            'status' => 'draft',
            'file_faktur' => null,
        ]);

        Efaktur::create([
            'nomor_faktur' => '010.000-26.000003',
            'tanggal_faktur' => Carbon::now()->subDays(2),
            'tipe' => 'Keluaran',
            'npwp_lawan' => '07.111.222.3-444.000',
            'nama_lawan' => 'CV Transport Jaya',
            'dpp' => 7500000,
            'ppn' => 825000,
            'ppnbm' => 0,
            'status' => 'terbit',
            'file_faktur' => null,
        ]);
    }
}