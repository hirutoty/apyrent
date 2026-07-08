<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SalesOrderSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['order_no' => 'SO-2026-001', 'tanggal' => '2026-01-20', 'pelanggan' => 'PT Maju Bersama',    'produk_jasa' => 'Sewa Minibus 2 Unit',    'qty' => 2, 'total_harga' => 10000000,  'status_order' => 'Selesai',    'metode_pembayaran' => 'Transfer Bank', 'sales' => 'Andi'],
            ['order_no' => 'SO-2026-002', 'tanggal' => '2026-02-05', 'pelanggan' => 'PT Global Trans',    'produk_jasa' => 'Sewa Bus Besar',         'qty' => 1, 'total_harga' => 15000000,  'status_order' => 'Selesai',    'metode_pembayaran' => 'Transfer Bank', 'sales' => 'Budi'],
            ['order_no' => 'SO-2026-003', 'tanggal' => '2026-02-18', 'pelanggan' => 'CV Karya Indah',     'produk_jasa' => 'Sewa Truk',              'qty' => 1, 'total_harga' => 8000000,   'status_order' => 'Diproses',   'metode_pembayaran' => 'Tempo',         'sales' => 'Cici'],
            ['order_no' => 'SO-2026-004', 'tanggal' => '2026-03-01', 'pelanggan' => 'PT Nusantara Raya',  'produk_jasa' => 'Sewa Minibus',           'qty' => 2, 'total_harga' => 11000000,  'status_order' => 'Selesai',    'metode_pembayaran' => 'Kredit',        'sales' => 'Dani'],
            ['order_no' => 'SO-2026-005', 'tanggal' => '2026-03-10', 'pelanggan' => 'CV Jaya Mandiri',    'produk_jasa' => 'Sewa MPV 4 Unit',        'qty' => 4, 'total_harga' => 16000000,  'status_order' => 'Diproses',   'metode_pembayaran' => 'Transfer Bank', 'sales' => 'Andi'],
            ['order_no' => 'SO-2026-006', 'tanggal' => '2026-03-25', 'pelanggan' => 'PT Berlian Trans',   'produk_jasa' => 'Sewa Bus Medium 2 Unit', 'qty' => 2, 'total_harga' => 20000000,  'status_order' => 'Selesai',    'metode_pembayaran' => 'Transfer Bank', 'sales' => 'Budi'],
            ['order_no' => 'SO-2026-007', 'tanggal' => '2026-04-05', 'pelanggan' => 'CV Mitra Logistik',  'produk_jasa' => 'Sewa Truk 3 Unit',       'qty' => 3, 'total_harga' => 22500000,  'status_order' => 'Dibatalkan', 'metode_pembayaran' => 'Transfer Bank', 'sales' => 'Cici'],
            ['order_no' => 'SO-2026-008', 'tanggal' => '2026-04-20', 'pelanggan' => 'PT Sejahtera Abadi', 'produk_jasa' => 'Sewa Sedan',             'qty' => 3, 'total_harga' => 10500000,  'status_order' => 'Diproses',   'metode_pembayaran' => 'Cash',          'sales' => 'Dani'],
            ['order_no' => 'SO-2026-009', 'tanggal' => '2026-05-08', 'pelanggan' => 'PT Prima Raya',      'produk_jasa' => 'Sewa SUV',               'qty' => 2, 'total_harga' => 12000000,  'status_order' => 'Selesai',    'metode_pembayaran' => 'Transfer Bank', 'sales' => 'Andi'],
            ['order_no' => 'SO-2026-010', 'tanggal' => '2026-05-20', 'pelanggan' => 'PT Sinar Harapan',   'produk_jasa' => 'Sewa Minibus',           'qty' => 1, 'total_harga' => 5500000,   'status_order' => 'Diproses',   'metode_pembayaran' => 'Tempo',         'sales' => 'Budi'],
        ];

        foreach ($data as $row) {
            DB::table('sales_orders')->insert(array_merge($row, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
