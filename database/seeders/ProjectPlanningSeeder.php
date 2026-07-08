<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProjectPlanning;

class ProjectPlanningSeeder extends Seeder
{
    public function run(): void
    {
        ProjectPlanning::insert([
            ['kode_proyek' => 'PRJ001', 'tahapan' => 'Survey & Perencanaan',   'tgl_mulai' => '2026-01-01', 'tgl_selesai' => '2026-01-07', 'durasi' => 7,  'pic' => 'Tim GA',    'status' => 'Selesai'],
            ['kode_proyek' => 'PRJ001', 'tahapan' => 'Pengadaan Material',     'tgl_mulai' => '2026-01-08', 'tgl_selesai' => '2026-01-20', 'durasi' => 12, 'pic' => 'Rudi',      'status' => 'Selesai'],
            ['kode_proyek' => 'PRJ001', 'tahapan' => 'Konstruksi',             'tgl_mulai' => '2026-01-21', 'tgl_selesai' => '2026-03-15', 'durasi' => 53, 'pic' => 'Kontraktor','status' => 'Berjalan'],
            ['kode_proyek' => 'PRJ001', 'tahapan' => 'Finishing & Serahterima','tgl_mulai' => '2026-03-16', 'tgl_selesai' => '2026-03-31', 'durasi' => 15, 'pic' => 'Rudi',      'status' => 'Plan'],
            ['kode_proyek' => 'PRJ002', 'tahapan' => 'Seleksi Vendor Bus',     'tgl_mulai' => '2026-02-01', 'tgl_selesai' => '2026-02-15', 'durasi' => 14, 'pic' => 'Rina',      'status' => 'Selesai'],
            ['kode_proyek' => 'PRJ002', 'tahapan' => 'Negosiasi & Kontrak',    'tgl_mulai' => '2026-02-16', 'tgl_selesai' => '2026-02-28', 'durasi' => 12, 'pic' => 'Rina',      'status' => 'Berjalan'],
            ['kode_proyek' => 'PRJ003', 'tahapan' => 'Instalasi Perangkat GPS','tgl_mulai' => '2026-01-15', 'tgl_selesai' => '2026-02-28', 'durasi' => 44, 'pic' => 'Ivan',      'status' => 'Berjalan'],
            ['kode_proyek' => 'PRJ003', 'tahapan' => 'Uji Coba & Training',    'tgl_mulai' => '2026-03-01', 'tgl_selesai' => '2026-03-31', 'durasi' => 30, 'pic' => 'Ivan',      'status' => 'Plan'],
        ]);
    }
}
