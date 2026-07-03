<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [

            [
                'user_id' => 1,
                'nama_service' => 'Ganti Oli Mesin',
                'biaya_default' => 350000,
            ],

            [
                'user_id' => 1,
                'nama_service' => 'Tune Up',
                'biaya_default' => 500000,
            ],

            [
                'user_id' => 1,
                'nama_service' => 'Spooring Balancing',
                'biaya_default' => 250000,
            ],

            [
                'user_id' => 1,
                'nama_service' => 'Ganti Kampas Rem',
                'biaya_default' => 450000,
            ],

            [
                'user_id' => 1,
                'nama_service' => 'Service AC',
                'biaya_default' => 600000,
            ],

            [
                'user_id' => 1,
                'nama_service' => 'Ganti Ban',
                'biaya_default' => 900000,
            ],

            [
                'user_id' => 1,
                'nama_service' => 'Overhaul Mesin',
                'biaya_default' => 4500000,
            ],

            [
                'user_id' => 1,
                'nama_service' => 'Cuci Mobil Premium',
                'biaya_default' => 75000,
            ],

            [
                'user_id' => 1,
                'nama_service' => 'Ganti Aki',
                'biaya_default' => 1200000,
            ],

            [
                'user_id' => 1,
                'nama_service' => 'Perbaikan Suspensi',
                'biaya_default' => 1800000,
            ],

        ];

        foreach ($data as $item) {
            Service::create($item);
        }
    }
}