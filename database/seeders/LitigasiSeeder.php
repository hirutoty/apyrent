<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Litigasi;

class LitigasiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['kasus' => 'Sengketa Kontrak Vendor', 'lawan' => 'PT Sumber Jaya', 'jenis_kasus' => 'Perdata', 'status' => 'Proses', 'pengacara' => 'Ahmad, SH', 'tanggal_sidang' => '2024-03-15'],
            ['kasus' => 'Gugatan Karyawan PHK', 'lawan' => 'Budi Santoso', 'jenis_kasus' => 'Ketenagakerjaan', 'status' => 'Selesai', 'pengacara' => 'Rina, SH', 'tanggal_sidang' => '2023-11-20'],
            ['kasus' => 'Sengketa Hak Merek', 'lawan' => 'CV Tirta Mandiri', 'jenis_kasus' => 'HKI', 'status' => 'Proses', 'pengacara' => 'Dodi, SH', 'tanggal_sidang' => '2024-05-10'],
            ['kasus' => 'Wanprestasi Pembayaran', 'lawan' => 'UD Karya Abadi', 'jenis_kasus' => 'Perdata', 'status' => 'Mediasi', 'pengacara' => 'Ahmad, SH', 'tanggal_sidang' => '2024-04-22'],
            ['kasus' => 'Pelanggaran NDA', 'lawan' => 'Mantan Karyawan', 'jenis_kasus' => 'Pidana', 'status' => 'Menunggu', 'pengacara' => 'Rina, SH', 'tanggal_sidang' => '2024-06-01'],
        ];
        foreach ($data as $item) {
            Litigasi::create($item);
        }
    }
}
