<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReturPenjualanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['no_retur' => 'RTR-001', 'tanggal' => '2026-02-10', 'no_order' => 'SO-2026-001', 'pelanggan' => 'PT Maju Bersama',    'produk' => 'Sewa Minibus', 'qty' => 1, 'alasan' => 'Unit mengalami kerusakan',          'nilai_retur' => 5000000,  'status' => 'Selesai'],
            ['no_retur' => 'RTR-002', 'tanggal' => '2026-02-20', 'no_order' => 'SO-2026-002', 'pelanggan' => 'PT Global Trans',    'produk' => 'Sewa Bus',     'qty' => 1, 'alasan' => 'Spesifikasi tidak sesuai',          'nilai_retur' => 15000000, 'status' => 'Diproses'],
            ['no_retur' => 'RTR-003', 'tanggal' => '2026-03-05', 'no_order' => 'SO-2026-003', 'pelanggan' => 'CV Karya Indah',     'produk' => 'Sewa Truk',    'qty' => 1, 'alasan' => 'Truk bermasalah di tengah jalan',   'nilai_retur' => 8000000,  'status' => 'Selesai'],
            ['no_retur' => 'RTR-004', 'tanggal' => '2026-03-18', 'no_order' => 'SO-2026-004', 'pelanggan' => 'PT Nusantara Raya',  'produk' => 'Sewa Minibus', 'qty' => 1, 'alasan' => 'AC tidak berfungsi',               'nilai_retur' => 5500000,  'status' => 'Menunggu'],
            ['no_retur' => 'RTR-005', 'tanggal' => '2026-04-02', 'no_order' => 'SO-2026-006', 'pelanggan' => 'PT Berlian Trans',   'produk' => 'Sewa Bus',     'qty' => 1, 'alasan' => 'Pembatalan sebagian order',         'nilai_retur' => 10000000, 'status' => 'Selesai'],
            ['no_retur' => 'RTR-006', 'tanggal' => '2026-04-15', 'no_order' => 'SO-2026-005', 'pelanggan' => 'CV Jaya Mandiri',    'produk' => 'Sewa MPV',     'qty' => 2, 'alasan' => 'Unit terlambat pengiriman',         'nilai_retur' => 8000000,  'status' => 'Diproses'],
            ['no_retur' => 'RTR-007', 'tanggal' => '2026-05-01', 'no_order' => 'SO-2026-008', 'pelanggan' => 'PT Sejahtera Abadi', 'produk' => 'Sewa Sedan',   'qty' => 1, 'alasan' => 'Kendaraan tidak sesuai pesanan',   'nilai_retur' => 3500000,  'status' => 'Menunggu'],
            ['no_retur' => 'RTR-008', 'tanggal' => '2026-05-15', 'no_order' => 'SO-2026-009', 'pelanggan' => 'PT Prima Raya',      'produk' => 'Sewa SUV',     'qty' => 1, 'alasan' => 'Kondisi kendaraan buruk',          'nilai_retur' => 6000000,  'status' => 'Selesai'],
        ];

        foreach ($data as $row) {
            DB::table('retur_penjualans')->insert(array_merge($row, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
