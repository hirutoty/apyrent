<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jenis;

class JenisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Jenis::create([
            'user_id' => 1,
            'nama_jenis' => 'Mobil SUV',
        ]);

        Jenis::create([
            'user_id' => 1,
            'nama_jenis' => 'Mobil MPV',
        ]);

        Jenis::create([
            'user_id' => 1,
            'nama_jenis' => 'Mobil Sedan',
        ]);

        Jenis::create([
            'user_id' => 1,
            'nama_jenis' => 'Pickup',
        ]);

        Jenis::create([
            'user_id' => 1,
            'nama_jenis' => 'Truck',
        ]);

        Jenis::create([
            'user_id' => 1,
            'nama_jenis' => 'Bus Pariwisata',
        ]);
    }
}