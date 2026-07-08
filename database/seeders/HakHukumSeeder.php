<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HakHukum;

class HakHukumSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['jenis_akses' => 'Baca', 'kategori_dokumen' => 'Kontrak', 'penerima_akses' => 'Staff Operasional', 'level_hak' => 'Terbatas', 'tanggal_akses' => '2024-01-01', 'status' => 'Aktif'],
            ['jenis_akses' => 'Baca & Edit', 'kategori_dokumen' => 'Perizinan', 'penerima_akses' => 'Manajer Legal', 'level_hak' => 'Penuh', 'tanggal_akses' => '2024-01-01', 'status' => 'Aktif'],
            ['jenis_akses' => 'Baca', 'kategori_dokumen' => 'Akta', 'penerima_akses' => 'Direktur', 'level_hak' => 'Penuh', 'tanggal_akses' => '2023-06-15', 'status' => 'Aktif'],
            ['jenis_akses' => 'Baca', 'kategori_dokumen' => 'Pajak', 'penerima_akses' => 'Staff Keuangan', 'level_hak' => 'Terbatas', 'tanggal_akses' => '2024-02-10', 'status' => 'Aktif'],
            ['jenis_akses' => 'Tidak Ada Akses', 'kategori_dokumen' => 'Litigasi', 'penerima_akses' => 'Staff Umum', 'level_hak' => 'Tidak Ada', 'tanggal_akses' => '2024-01-01', 'status' => 'Non-Aktif'],
        ];
        foreach ($data as $item) {
            HakHukum::create($item);
        }
    }
}
