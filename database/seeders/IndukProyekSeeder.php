<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IndukProyek;

class IndukProyekSeeder extends Seeder
{
    public function run(): void
    {
        IndukProyek::insert([
            ['kode' => 'PRJ001', 'nama_proyek' => 'Renovasi Pool Kendaraan Bekasi',    'jenis' => 'Internal',  'klien_vendor' => '-',              'pic' => 'Rudi',  'status' => 'Berjalan', 'mulai' => '2026-01-01', 'target_selesai' => '2026-03-31', 'progres' => '65%', 'nilai_proyek' => 450000000,  'lokasi' => 'Bekasi'],
            ['kode' => 'PRJ002', 'nama_proyek' => 'Pengadaan Armada Bus Pariwisata',    'jenis' => 'Internal',  'klien_vendor' => '-',              'pic' => 'Rina',  'status' => 'Approved', 'mulai' => '2026-02-01', 'target_selesai' => '2026-04-30', 'progres' => '20%', 'nilai_proyek' => 1500000000, 'lokasi' => 'Jakarta'],
            ['kode' => 'PRJ003', 'nama_proyek' => 'Sistem GPS & Monitoring Armada',     'jenis' => 'Internal',  'klien_vendor' => 'PT TechMaps',    'pic' => 'Ivan',  'status' => 'Berjalan', 'mulai' => '2026-01-15', 'target_selesai' => '2026-05-15', 'progres' => '45%', 'nilai_proyek' => 210000000,  'lokasi' => 'Bandung'],
            ['kode' => 'PRJ004', 'nama_proyek' => 'Renovasi Kantor Pusat Tangerang',    'jenis' => 'Internal',  'klien_vendor' => '-',              'pic' => 'Sari',  'status' => 'Plan',     'mulai' => '2026-04-01', 'target_selesai' => '2026-06-30', 'progres' => '0%',  'nilai_proyek' => 320000000,  'lokasi' => 'Tangerang'],
            ['kode' => 'PRJ005', 'nama_proyek' => 'Layanan Antar Jemput PT Sinar Abadi','jenis' => 'Eksternal', 'klien_vendor' => 'PT Sinar Abadi', 'pic' => 'Andi',  'status' => 'Berjalan', 'mulai' => '2026-02-15', 'target_selesai' => '2026-12-31', 'progres' => '30%', 'nilai_proyek' => 850000000,  'lokasi' => 'Surabaya'],
            ['kode' => 'PRJ006', 'nama_proyek' => 'Workshop Pelatihan Driver Safety',   'jenis' => 'Internal',  'klien_vendor' => '-',              'pic' => 'Budi',  'status' => 'Selesai',  'mulai' => '2025-12-01', 'target_selesai' => '2025-12-31', 'progres' => '100%','nilai_proyek' => 75000000,   'lokasi' => 'Jakarta'],
        ]);
    }
}
