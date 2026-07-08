<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PricelistDiskonSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['id_harga' => 'PRC-001', 'nama_produk' => 'Sewa Sedan',      'level_pelanggan' => 'Regular',  'harga_normal' => 3500000,  'diskon' => 0,   'harga_diskon' => 3500000,  'periode_mulai' => '2026-01-01', 'periode_selesai' => '2026-12-31', 'status' => 'Aktif'],
            ['id_harga' => 'PRC-002', 'nama_produk' => 'Sewa Sedan',      'level_pelanggan' => 'Silver',   'harga_normal' => 3500000,  'diskon' => 5,   'harga_diskon' => 3325000,  'periode_mulai' => '2026-01-01', 'periode_selesai' => '2026-12-31', 'status' => 'Aktif'],
            ['id_harga' => 'PRC-003', 'nama_produk' => 'Sewa Sedan',      'level_pelanggan' => 'Gold',     'harga_normal' => 3500000,  'diskon' => 10,  'harga_diskon' => 3150000,  'periode_mulai' => '2026-01-01', 'periode_selesai' => '2026-12-31', 'status' => 'Aktif'],
            ['id_harga' => 'PRC-004', 'nama_produk' => 'Sewa Minibus',    'level_pelanggan' => 'Regular',  'harga_normal' => 5000000,  'diskon' => 0,   'harga_diskon' => 5000000,  'periode_mulai' => '2026-01-01', 'periode_selesai' => '2026-12-31', 'status' => 'Aktif'],
            ['id_harga' => 'PRC-005', 'nama_produk' => 'Sewa Minibus',    'level_pelanggan' => 'Gold',     'harga_normal' => 5000000,  'diskon' => 10,  'harga_diskon' => 4500000,  'periode_mulai' => '2026-01-01', 'periode_selesai' => '2026-12-31', 'status' => 'Aktif'],
            ['id_harga' => 'PRC-006', 'nama_produk' => 'Sewa Minibus',    'level_pelanggan' => 'Platinum', 'harga_normal' => 5000000,  'diskon' => 15,  'harga_diskon' => 4250000,  'periode_mulai' => '2026-01-01', 'periode_selesai' => '2026-12-31', 'status' => 'Aktif'],
            ['id_harga' => 'PRC-007', 'nama_produk' => 'Sewa Truk',       'level_pelanggan' => 'Regular',  'harga_normal' => 8000000,  'diskon' => 0,   'harga_diskon' => 8000000,  'periode_mulai' => '2026-01-01', 'periode_selesai' => '2026-12-31', 'status' => 'Aktif'],
            ['id_harga' => 'PRC-008', 'nama_produk' => 'Sewa Truk',       'level_pelanggan' => 'Platinum', 'harga_normal' => 8000000,  'diskon' => 12,  'harga_diskon' => 7040000,  'periode_mulai' => '2026-01-01', 'periode_selesai' => '2026-12-31', 'status' => 'Aktif'],
            ['id_harga' => 'PRC-009', 'nama_produk' => 'Sewa Bus Besar',  'level_pelanggan' => 'Regular',  'harga_normal' => 15000000, 'diskon' => 0,   'harga_diskon' => 15000000, 'periode_mulai' => '2026-01-01', 'periode_selesai' => '2026-12-31', 'status' => 'Aktif'],
            ['id_harga' => 'PRC-010', 'nama_produk' => 'Sewa Bus Besar',  'level_pelanggan' => 'Gold',     'harga_normal' => 15000000, 'diskon' => 8,   'harga_diskon' => 13800000, 'periode_mulai' => '2026-01-01', 'periode_selesai' => '2026-12-31', 'status' => 'Tidak Aktif'],
        ];

        foreach ($data as $row) {
            DB::table('pricelist_diskons')->insert(array_merge($row, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
