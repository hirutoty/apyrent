<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LaporanKeuangan;
use Carbon\Carbon;

class LaporanKeuanganSeeder extends Seeder
{
    public function run(): void
    {
        LaporanKeuangan::create([
            'nama_perusahaan' => 'APY Rental',
            'pendapatan' => 25000000,
            'beban' => 12000000,
            'laba' => 13000000,
            'periode' => Carbon::now()->format('Y-m'),
        ]);

        LaporanKeuangan::create([
            'nama_perusahaan' => 'APY Rental',
            'pendapatan' => 30000000,
            'beban' => 15000000,
            'laba' => 15000000,
            'periode' => Carbon::now()->subMonth()->format('Y-m'),
        ]);

        LaporanKeuangan::create([
            'nama_perusahaan' => 'APY Rental',
            'pendapatan' => 18000000,
            'beban' => 9000000,
            'laba' => 9000000,
            'periode' => Carbon::now()->subMonths(2)->format('Y-m'),
        ]);
    }
}