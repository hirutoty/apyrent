<?php

namespace Database\Seeders;

use App\Models\Sosmedp;
use Illuminate\Database\Seeder;

class SosmedpSeeder extends Seeder
{
    public function run(): void
    {
        Sosmedp::create([
            'id_kampanye' => 'SMP001',
            'channel' => 'Instagram',
            'utm_source' => 'instagram',
            'utm_campaign' => 'promo_rental_july',
            'klik' => 320,
            'konversi' => 18,
            'total_biaya' => 1500000,
            'total_penjualan' => 9000000,
            'roi' => 500.00,
        ]);

        Sosmedp::create([
            'id_kampanye' => 'SMP002',
            'channel' => 'Facebook',
            'utm_source' => 'facebook',
            'utm_campaign' => 'awareness_apyrent',
            'klik' => 580,
            'konversi' => 25,
            'total_biaya' => 2000000,
            'total_penjualan' => 12500000,
            'roi' => 525.00,
        ]);
    }
}
