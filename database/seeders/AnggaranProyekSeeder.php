<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AnggaranProyek;

class AnggaranProyekSeeder extends Seeder
{
    public function run(): void
    {
        $data = [

            [
                'proyek' => 'Pembangunan Sistem Rental',
                'kategori' => 'Development',
                'budget' => 15000000,
                'realisasi' => 6000000,
            ],

            [
                'proyek' => 'Server & Hosting',
                'kategori' => 'Infrastructure',
                'budget' => 5000000,
                'realisasi' => 2500000,
            ],

            [
                'proyek' => 'Pembelian GPS',
                'kategori' => 'Operasional',
                'budget' => 10000000,
                'realisasi' => 7500000,
            ],

            [
                'proyek' => 'Promosi Rental',
                'kategori' => 'Marketing',
                'budget' => 7000000,
                'realisasi' => 3000000,
            ],

            [
                'proyek' => 'Service Kendaraan',
                'kategori' => 'Maintenance',
                'budget' => 12000000,
                'realisasi' => 4500000,
            ],

        ];

        foreach ($data as $d) {

            $sisa = $d['budget'] - $d['realisasi'];

            $persen = 0;

            if ($d['budget'] > 0) {
                $persen = ($d['realisasi'] / $d['budget']) * 100;
            }

            AnggaranProyek::create([
                'proyek' => $d['proyek'],
                'kategori' => $d['kategori'],
                'budget' => $d['budget'],
                'realisasi' => $d['realisasi'],
                'sisa' => $sisa,
                'persen_terpakai' => $persen,
            ]);
        }
    }
}