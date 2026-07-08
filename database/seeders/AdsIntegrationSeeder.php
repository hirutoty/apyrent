<?php

namespace Database\Seeders;

use App\Models\AdsIntegration;
use Illuminate\Database\Seeder;

class AdsIntegrationSeeder extends Seeder
{
    public function run(): void
    {
        AdsIntegration::create([
            'id_iklan' => 'ADS001',
            'nama_iklan' => 'Google Ads - Rental Mobil Jakarta',
            'platform' => 'Google Ads',
            'tanggal_aktif' => '2026-07-01',
            'budget_harian' => 500000,
            'klik' => 350,
            'konversi' => 28,
            'biaya_total' => 15000000,
            'penjualan' => 70000000,
            'roi' => '367%',
        ]);

        AdsIntegration::create([
            'id_iklan' => 'ADS002',
            'nama_iklan' => 'Facebook Ads - Awareness Campaign',
            'platform' => 'Meta Ads',
            'tanggal_aktif' => '2026-07-05',
            'budget_harian' => 300000,
            'klik' => 520,
            'konversi' => 35,
            'biaya_total' => 9000000,
            'penjualan' => 52500000,
            'roi' => '483%',
        ]);
    }
}
