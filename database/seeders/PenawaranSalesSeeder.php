<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenawaranSalesSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['no_quotation' => 'QUO-2026-001', 'tanggal' => '2026-01-15', 'pelanggan' => 'PT Maju Bersama',    'produk_jasa' => 'Sewa Minibus',     'jumlah' => 2, 'harga_satuan' => 5000000,  'total_harga' => 10000000,  'status' => 'Disetujui', 'valid_sampai' => '2026-02-15'],
            ['no_quotation' => 'QUO-2026-002', 'tanggal' => '2026-01-20', 'pelanggan' => 'CV Karya Indah',     'produk_jasa' => 'Sewa Truk',        'jumlah' => 1, 'harga_satuan' => 8000000,  'total_harga' => 8000000,   'status' => 'Terkirim',  'valid_sampai' => '2026-02-20'],
            ['no_quotation' => 'QUO-2026-003', 'tanggal' => '2026-02-01', 'pelanggan' => 'PT Sejahtera Abadi', 'produk_jasa' => 'Sewa Sedan',       'jumlah' => 3, 'harga_satuan' => 3500000,  'total_harga' => 10500000,  'status' => 'Draft',     'valid_sampai' => '2026-03-01'],
            ['no_quotation' => 'QUO-2026-004', 'tanggal' => '2026-02-10', 'pelanggan' => 'PT Global Trans',    'produk_jasa' => 'Sewa Bus Besar',   'jumlah' => 1, 'harga_satuan' => 15000000, 'total_harga' => 15000000,  'status' => 'Disetujui', 'valid_sampai' => '2026-03-10'],
            ['no_quotation' => 'QUO-2026-005', 'tanggal' => '2026-02-25', 'pelanggan' => 'CV Jaya Mandiri',    'produk_jasa' => 'Sewa MPV',         'jumlah' => 4, 'harga_satuan' => 4000000,  'total_harga' => 16000000,  'status' => 'Terkirim',  'valid_sampai' => '2026-03-25'],
            ['no_quotation' => 'QUO-2026-006', 'tanggal' => '2026-03-05', 'pelanggan' => 'PT Nusantara Raya',  'produk_jasa' => 'Sewa Minibus',     'jumlah' => 2, 'harga_satuan' => 5500000,  'total_harga' => 11000000,  'status' => 'Disetujui', 'valid_sampai' => '2026-04-05'],
            ['no_quotation' => 'QUO-2026-007', 'tanggal' => '2026-03-15', 'pelanggan' => 'PT Sinar Harapan',   'produk_jasa' => 'Sewa SUV',         'jumlah' => 2, 'harga_satuan' => 6000000,  'total_harga' => 12000000,  'status' => 'Ditolak',   'valid_sampai' => '2026-04-15'],
            ['no_quotation' => 'QUO-2026-008', 'tanggal' => '2026-04-01', 'pelanggan' => 'CV Mitra Logistik',  'produk_jasa' => 'Sewa Truk',        'jumlah' => 3, 'harga_satuan' => 7500000,  'total_harga' => 22500000,  'status' => 'Terkirim',  'valid_sampai' => '2026-05-01'],
            ['no_quotation' => 'QUO-2026-009', 'tanggal' => '2026-04-20', 'pelanggan' => 'PT Berlian Trans',   'produk_jasa' => 'Sewa Bus Medium',  'jumlah' => 2, 'harga_satuan' => 10000000, 'total_harga' => 20000000,  'status' => 'Disetujui', 'valid_sampai' => '2026-05-20'],
            ['no_quotation' => 'QUO-2026-010', 'tanggal' => '2026-05-10', 'pelanggan' => 'PT Prima Raya',      'produk_jasa' => 'Sewa Sedan',       'jumlah' => 5, 'harga_satuan' => 3000000,  'total_harga' => 15000000,  'status' => 'Draft',     'valid_sampai' => '2026-06-10'],
        ];

        foreach ($data as $row) {
            DB::table('penawarans')->insert(array_merge($row, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
