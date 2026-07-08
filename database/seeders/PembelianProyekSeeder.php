<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PembelianProyek;

class PembelianProyekSeeder extends Seeder
{
    public function run(): void
    {
        PembelianProyek::insert([
            ['pr_no' => 'PR-PRJ001-001', 'proyek' => 'PRJ001', 'item_diminta' => 'Semen Portland 40kg',    'qty' => 500,  'vendor' => 'PT Semen Indonesia',  'estimasi_harga' => 20000000,  'status' => 'Disetujui', 'tgl_permintaan' => '2026-01-08', 'created_at' => now(), 'updated_at' => now()],
            ['pr_no' => 'PR-PRJ001-002', 'proyek' => 'PRJ001', 'item_diminta' => 'Besi Beton 10mm',        'qty' => 200,  'vendor' => 'PT Krakatau Steel',    'estimasi_harga' => 35000000,  'status' => 'Disetujui', 'tgl_permintaan' => '2026-01-10', 'created_at' => now(), 'updated_at' => now()],
            ['pr_no' => 'PR-PRJ001-003', 'proyek' => 'PRJ001', 'item_diminta' => 'Bata Merah 20x10x5',    'qty' => 5000, 'vendor' => 'CV Bata Kuat',         'estimasi_harga' => 10000000,  'status' => 'Disetujui', 'tgl_permintaan' => '2026-01-12', 'created_at' => now(), 'updated_at' => now()],
            ['pr_no' => 'PR-PRJ001-004', 'proyek' => 'PRJ001', 'item_diminta' => 'Cat Tembok & Finishing', 'qty' => 50,   'vendor' => 'PT Nippon Paint',      'estimasi_harga' => 15000000,  'status' => 'Pending',   'tgl_permintaan' => '2026-02-20', 'created_at' => now(), 'updated_at' => now()],
            ['pr_no' => 'PR-PRJ002-001', 'proyek' => 'PRJ002', 'item_diminta' => 'Bus Pariwisata 32 Seat', 'qty' => 3,    'vendor' => 'PT Hino Motors',       'estimasi_harga' => 1200000000,'status' => 'Disetujui', 'tgl_permintaan' => '2026-02-05', 'created_at' => now(), 'updated_at' => now()],
            ['pr_no' => 'PR-PRJ002-002', 'proyek' => 'PRJ002', 'item_diminta' => 'Wrapping & Branding Bus','qty' => 3,    'vendor' => 'CV Kreatif Visual',    'estimasi_harga' => 15000000,  'status' => 'Pending',   'tgl_permintaan' => '2026-04-01', 'created_at' => now(), 'updated_at' => now()],
            ['pr_no' => 'PR-PRJ003-001', 'proyek' => 'PRJ003', 'item_diminta' => 'Unit GPS Tracker',       'qty' => 50,   'vendor' => 'PT TechMaps',          'estimasi_harga' => 100000000, 'status' => 'Disetujui', 'tgl_permintaan' => '2026-01-14', 'created_at' => now(), 'updated_at' => now()],
            ['pr_no' => 'PR-PRJ003-002', 'proyek' => 'PRJ003', 'item_diminta' => 'Server Dashboard Cloud', 'qty' => 1,    'vendor' => 'PT AWS Indonesia',     'estimasi_harga' => 24000000,  'status' => 'Disetujui', 'tgl_permintaan' => '2026-01-15', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
