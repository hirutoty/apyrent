<?php

namespace Database\Seeders;

use App\Models\Trackingutm;
use Illuminate\Database\Seeder;

class TrackingutmSeeder extends Seeder
{
    public function run(): void
    {
        Trackingutm::create([
            'kode_tracking' => 'UTM001',
            'url_tujuan' => 'https://apyrent.com/promo',
            'utm_source' => 'google',
            'utm_medium' => 'cpc',
            'utm_campaign' => 'rental_promo_q3',
            'utm_term' => 'sewa mobil jakarta',
            'utm_content' => 'text_ad_1',
            'total_klik' => 450,
            'total_konversi' => 32,
            'status' => 'Aktif',
        ]);

        Trackingutm::create([
            'kode_tracking' => 'UTM002',
            'url_tujuan' => 'https://apyrent.com/fleet',
            'utm_source' => 'email',
            'utm_medium' => 'newsletter',
            'utm_campaign' => 'new_cars_2026',
            'utm_term' => null,
            'utm_content' => 'banner_top',
            'total_klik' => 280,
            'total_konversi' => 19,
            'status' => 'Aktif',
        ]);
    }
}
