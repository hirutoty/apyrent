<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KomisiSalesSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama_sales' => 'Andi', 'bulan' => '2026-01', 'total_penjualan' => 45000000, 'persen_komisi' => 3,   'total_komisi' => 1350000, 'status_bayar' => 'Sudah Dibayar'],
            ['nama_sales' => 'Budi', 'bulan' => '2026-01', 'total_penjualan' => 38000000, 'persen_komisi' => 3,   'total_komisi' => 1140000, 'status_bayar' => 'Sudah Dibayar'],
            ['nama_sales' => 'Cici', 'bulan' => '2026-01', 'total_penjualan' => 52000000, 'persen_komisi' => 3.5, 'total_komisi' => 1820000, 'status_bayar' => 'Sudah Dibayar'],
            ['nama_sales' => 'Dani', 'bulan' => '2026-01', 'total_penjualan' => 29000000, 'persen_komisi' => 3,   'total_komisi' => 870000,  'status_bayar' => 'Sudah Dibayar'],
            ['nama_sales' => 'Andi', 'bulan' => '2026-02', 'total_penjualan' => 55000000, 'persen_komisi' => 3.5, 'total_komisi' => 1925000, 'status_bayar' => 'Sudah Dibayar'],
            ['nama_sales' => 'Budi', 'bulan' => '2026-02', 'total_penjualan' => 42000000, 'persen_komisi' => 3,   'total_komisi' => 1260000, 'status_bayar' => 'Sudah Dibayar'],
            ['nama_sales' => 'Cici', 'bulan' => '2026-02', 'total_penjualan' => 60000000, 'persen_komisi' => 4,   'total_komisi' => 2400000, 'status_bayar' => 'Sudah Dibayar'],
            ['nama_sales' => 'Dani', 'bulan' => '2026-02', 'total_penjualan' => 35000000, 'persen_komisi' => 3,   'total_komisi' => 1050000, 'status_bayar' => 'Sudah Dibayar'],
            ['nama_sales' => 'Andi', 'bulan' => '2026-03', 'total_penjualan' => 48000000, 'persen_komisi' => 3,   'total_komisi' => 1440000, 'status_bayar' => 'Sudah Dibayar'],
            ['nama_sales' => 'Budi', 'bulan' => '2026-03', 'total_penjualan' => 51000000, 'persen_komisi' => 3.5, 'total_komisi' => 1785000, 'status_bayar' => 'Sudah Dibayar'],
            ['nama_sales' => 'Cici', 'bulan' => '2026-03', 'total_penjualan' => 44000000, 'persen_komisi' => 3,   'total_komisi' => 1320000, 'status_bayar' => 'Belum Dibayar'],
            ['nama_sales' => 'Dani', 'bulan' => '2026-03', 'total_penjualan' => 39000000, 'persen_komisi' => 3,   'total_komisi' => 1170000, 'status_bayar' => 'Belum Dibayar'],
        ];

        foreach ($data as $row) {
            DB::table('komisi_sales')->insert(array_merge($row, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
