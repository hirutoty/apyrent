<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gps;

class GpsSeeder extends Seeder
{
    public function run(): void
    {
        $providers = [
            'GPS Tracker Pro', 'Teltonika', 'Queclink', 'Concox', 'Ruptela',
            'Coban', 'Gosafe', 'Jointech', 'Meitrack', 'Sinotrack',
        ];

        foreach ($providers as $i => $nama) {
            Gps::create([
                'user_id'          => 1,
                'nama_gps'         => $nama,
                'alamat'           => 'Jl. Teknologi No. ' . ($i + 1) . ', Jakarta',
                'nama_marketing'   => 'Marketing ' . ($i + 1),
                'kontak_marketing' => '08' . rand(100000000, 999999999),
                'nama_bengkel'     => 'Bengkel GPS ' . ($i + 1),
                'kontak_bengkel'   => '08' . rand(100000000, 999999999),
            ]);
        }
    }
}
