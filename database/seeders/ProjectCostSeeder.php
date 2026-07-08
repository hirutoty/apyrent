<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProjectCost;

class ProjectCostSeeder extends Seeder
{
    public function run(): void
    {
        ProjectCost::insert([
            ['proyek' => 'PRJ001', 'kategori_biaya' => 'Material Bangunan',   'estimasi' => 150000000, 'realisasi' => 142000000, 'selisih' => -8000000,  'status' => 'Efisien'],
            ['proyek' => 'PRJ001', 'kategori_biaya' => 'Upah Tenaga Kerja',   'estimasi' => 100000000, 'realisasi' => 115000000, 'selisih' => 15000000,  'status' => 'Over Budget'],
            ['proyek' => 'PRJ001', 'kategori_biaya' => 'Sewa Alat Berat',     'estimasi' => 50000000,  'realisasi' => 48000000,  'selisih' => -2000000,  'status' => 'Efisien'],
            ['proyek' => 'PRJ002', 'kategori_biaya' => 'Pembelian Unit Bus',   'estimasi' => 1200000000,'realisasi' => 1200000000,'selisih' => 0,         'status' => 'Normal'],
            ['proyek' => 'PRJ002', 'kategori_biaya' => 'Aksesoris & Modifikasi','estimasi'=> 80000000,  'realisasi' => 92000000,  'selisih' => 12000000,  'status' => 'Over Budget'],
            ['proyek' => 'PRJ003', 'kategori_biaya' => 'Perangkat GPS',        'estimasi' => 120000000, 'realisasi' => 118500000, 'selisih' => -1500000,  'status' => 'Efisien'],
            ['proyek' => 'PRJ003', 'kategori_biaya' => 'Biaya Instalasi',      'estimasi' => 30000000,  'realisasi' => 30000000,  'selisih' => 0,         'status' => 'Normal'],
            ['proyek' => 'PRJ005', 'kategori_biaya' => 'Biaya Operasional',    'estimasi' => 200000000, 'realisasi' => 185000000, 'selisih' => -15000000, 'status' => 'Efisien'],
        ]);
    }
}
