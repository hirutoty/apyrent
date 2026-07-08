<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DokumenProyek;

class DokumenProyekSeeder extends Seeder
{
    public function run(): void
    {
        DokumenProyek::insert([
            ['proyek' => 'PRJ001', 'nama_dokumen' => 'RAB Renovasi Pool',          'tipe' => 'XLSX', 'file' => '-', 'status' => 'Valid', 'tanggal_upload' => '2025-12-28', 'created_at' => now(), 'updated_at' => now()],
            ['proyek' => 'PRJ001', 'nama_dokumen' => 'Gambar Desain Konstruksi',   'tipe' => 'PDF',  'file' => '-', 'status' => 'Valid', 'tanggal_upload' => '2025-12-30', 'created_at' => now(), 'updated_at' => now()],
            ['proyek' => 'PRJ001', 'nama_dokumen' => 'Kontrak Kontraktor',         'tipe' => 'PDF',  'file' => '-', 'status' => 'Valid', 'tanggal_upload' => '2026-01-02', 'created_at' => now(), 'updated_at' => now()],
            ['proyek' => 'PRJ002', 'nama_dokumen' => 'Spesifikasi Teknis Bus',     'tipe' => 'PDF',  'file' => '-', 'status' => 'Valid', 'tanggal_upload' => '2026-01-20', 'created_at' => now(), 'updated_at' => now()],
            ['proyek' => 'PRJ002', 'nama_dokumen' => 'Purchase Order Bus',         'tipe' => 'PDF',  'file' => '-', 'status' => 'Draft', 'tanggal_upload' => '2026-02-05', 'created_at' => now(), 'updated_at' => now()],
            ['proyek' => 'PRJ003', 'nama_dokumen' => 'Proposal GPS Monitoring',    'tipe' => 'PDF',  'file' => '-', 'status' => 'Valid', 'tanggal_upload' => '2026-01-10', 'created_at' => now(), 'updated_at' => now()],
            ['proyek' => 'PRJ003', 'nama_dokumen' => 'Kontrak Vendor GPS',         'tipe' => 'PDF',  'file' => '-', 'status' => 'Valid', 'tanggal_upload' => '2026-01-14', 'created_at' => now(), 'updated_at' => now()],
            ['proyek' => 'PRJ005', 'nama_dokumen' => 'PKS Layanan Antar Jemput',  'tipe' => 'PDF',  'file' => '-', 'status' => 'Valid', 'tanggal_upload' => '2026-02-12', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
