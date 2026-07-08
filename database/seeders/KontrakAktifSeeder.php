<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KontrakAktif;

class KontrakAktifSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'kode_kontrak' => 'KNT-2024-001',
                'mitra'        => 'PT Astra International',
                'nilai'        => 150000000,
                'tgl_mulai'    => '2024-01-01',
                'tgl_selesai'  => '2025-12-31',
                'pic'          => 'Budi Santoso',
                'status'       => 'Aktif',
                'perpanjangan' => true,
            ],
            [
                'kode_kontrak' => 'KNT-2024-002',
                'mitra'        => 'CV Maju Bersama',
                'nilai'        => 75000000,
                'tgl_mulai'    => '2024-03-01',
                'tgl_selesai'  => '2025-02-28',
                'pic'          => 'Sari Dewi',
                'status'       => 'Aktif',
                'perpanjangan' => false,
            ],
            [
                'kode_kontrak' => 'KNT-2024-003',
                'mitra'        => 'PT Teknologi Nusantara',
                'nilai'        => 250000000,
                'tgl_mulai'    => '2024-06-01',
                'tgl_selesai'  => '2026-05-31',
                'pic'          => 'Andi Wijaya',
                'status'       => 'Aktif',
                'perpanjangan' => true,
            ],
            [
                'kode_kontrak' => 'KNT-2023-010',
                'mitra'        => 'UD Karya Mandiri',
                'nilai'        => 30000000,
                'tgl_mulai'    => '2023-07-01',
                'tgl_selesai'  => '2024-06-30',
                'pic'          => 'Rina Kusuma',
                'status'       => 'Selesai',
                'perpanjangan' => false,
            ],
            [
                'kode_kontrak' => 'KNT-2025-001',
                'mitra'        => 'PT Logistik Andalan',
                'nilai'        => 90000000,
                'tgl_mulai'    => '2025-01-01',
                'tgl_selesai'  => '2025-12-31',
                'pic'          => 'Doni Prasetyo',
                'status'       => 'Draft',
                'perpanjangan' => false,
            ],
        ];

        foreach ($data as $item) {
            KontrakAktif::create($item);
        }
    }
}
