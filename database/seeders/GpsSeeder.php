<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gps;

class GpsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Gps::create([
            'user_id' => 1,
            'nama_gps' => 'GPS Tracker Nusantara',
            'alamat' => 'Jl. Ahmad Yani No. 12 Wonosobo',
            'nama_marketing' => 'Rizky Pratama',
            'kontak_marketing' => '081234567890',
            'nama_bengkel' => 'Bengkel GPS Wonosobo',
            'kontak_bengkel' => '082233445566',
        ]);

        Gps::create([
            'user_id' => 1,
            'nama_gps' => 'GPS Satelit Indonesia',
            'alamat' => 'Jl. Dieng No. 45 Banjarnegara',
            'nama_marketing' => 'Andi Saputra',
            'kontak_marketing' => '081298765432',
            'nama_bengkel' => 'Bengkel Satelit Banjarnegara',
            'kontak_bengkel' => '082112223333',
        ]);

        Gps::create([
            'user_id' => 1,
            'nama_gps' => 'Global Tracker GPS',
            'alamat' => 'Jl. Soekarno Hatta No. 8 Magelang',
            'nama_marketing' => 'Fajar Nugroho',
            'kontak_marketing' => '081377788899',
            'nama_bengkel' => 'Tracker Service Magelang',
            'kontak_bengkel' => '083344556677',
        ]);

        Gps::create([
            'user_id' => 1,
            'nama_gps' => 'Smart GPS Solution',
            'alamat' => 'Jl. Veteran No. 22 Temanggung',
            'nama_marketing' => 'Dimas Setiawan',
            'kontak_marketing' => '081355566677',
            'nama_bengkel' => 'Smart GPS Garage',
            'kontak_bengkel' => '082266778899',
        ]);
    }
}