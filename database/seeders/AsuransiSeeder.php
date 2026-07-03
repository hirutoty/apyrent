<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Asuransi;

class AsuransiSeeder extends Seeder
{
    public function run(): void
    {
        Asuransi::create([
            'user_id' => 1,
            'nama_asuransi' => 'BCA Insurance',
            'alamat' => 'Jl. Sudirman No. 10 Jakarta',
            'nama_marketing' => 'Andi Saputra',
            'kontak_marketing' => '081234567890',
            'nama_bengkel' => 'Bengkel Maju Motor',
            'kontak_bengkel' => '082233445566',
        ]);

        Asuransi::create([
            'user_id' => 1,
            'nama_asuransi' => 'Adira Insurance',
            'alamat' => 'Jl. Malioboro No. 20 Yogyakarta',
            'nama_marketing' => 'Budi Hartono',
            'kontak_marketing' => '081298765432',
            'nama_bengkel' => 'Bengkel Jaya Abadi',
            'kontak_bengkel' => '085566778899',
        ]);

        Asuransi::create([
            'user_id' => 1,
            'nama_asuransi' => 'ACA Insurance',
            'alamat' => 'Jl. Pemuda No. 12 Semarang',
            'nama_marketing' => 'Siti Rahma',
            'kontak_marketing' => '087712345678',
            'nama_bengkel' => 'Bengkel Berkah Mobil',
            'kontak_bengkel' => '081122334455',
        ]);
    }
}