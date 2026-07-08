<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProjectTimeline;

class ProjectTimelineSeeder extends Seeder
{
    public function run(): void
    {
        ProjectTimeline::insert([
            ['proyek' => 'PRJ001', 'kegiatan' => 'Pengecoran Lantai Garasi',      'deadline' => '2026-02-10', 'reminder' => true,  'status' => 'Selesai',   'created_at' => now(), 'updated_at' => now()],
            ['proyek' => 'PRJ001', 'kegiatan' => 'Pemasangan Atap Baja Ringan',   'deadline' => '2026-02-28', 'reminder' => true,  'status' => 'Berjalan',  'created_at' => now(), 'updated_at' => now()],
            ['proyek' => 'PRJ001', 'kegiatan' => 'Pemasangan Listrik & CCTV',     'deadline' => '2026-03-10', 'reminder' => true,  'status' => 'Scheduled', 'created_at' => now(), 'updated_at' => now()],
            ['proyek' => 'PRJ002', 'kegiatan' => 'Pembayaran DP Pembelian Bus',   'deadline' => '2026-02-20', 'reminder' => true,  'status' => 'Selesai',   'created_at' => now(), 'updated_at' => now()],
            ['proyek' => 'PRJ002', 'kegiatan' => 'Pengiriman Unit Bus ke Pool',   'deadline' => '2026-04-15', 'reminder' => true,  'status' => 'Scheduled', 'created_at' => now(), 'updated_at' => now()],
            ['proyek' => 'PRJ003', 'kegiatan' => 'Pemasangan GPS 20 Unit Sedan',  'deadline' => '2026-02-15', 'reminder' => false, 'status' => 'Berjalan',  'created_at' => now(), 'updated_at' => now()],
            ['proyek' => 'PRJ003', 'kegiatan' => 'Aktivasi Dashboard Monitoring', 'deadline' => '2026-03-15', 'reminder' => true,  'status' => 'Scheduled', 'created_at' => now(), 'updated_at' => now()],
            ['proyek' => 'PRJ005', 'kegiatan' => 'Mulai Operasional Rute I',      'deadline' => '2026-03-01', 'reminder' => false, 'status' => 'Selesai',   'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
