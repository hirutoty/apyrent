<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RekonsiliasiBank;
use Carbon\Carbon;

class RekonsiliasiBankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [

            [
                'tanggal'             => Carbon::now()->subDays(12),
                'deskripsi'           => 'Pembayaran rental masuk',
                'reference_no'        => 'BANK-INV-001',
                'amount'              => 1500000,
                'currency'            => 'IDR',
                'status_rekonsiliasi' => 'matched',
                'invoice_id'          => null,
            ],

            [
                'tanggal'             => Carbon::now()->subDays(9),
                'deskripsi'           => 'Transfer service kendaraan',
                'reference_no'        => 'BANK-INV-002',
                'amount'              => 500000,
                'currency'            => 'IDR',
                'status_rekonsiliasi' => 'matched',
                'invoice_id'          => null,
            ],

            [
                'tanggal'             => Carbon::now()->subDays(6),
                'deskripsi'           => 'Pembayaran deposit rental',
                'reference_no'        => 'BANK-INV-003',
                'amount'              => 2000000,
                'currency'            => 'IDR',
                'status_rekonsiliasi' => 'Pending',
                'invoice_id'          => null,
            ],

            [
                'tanggal'             => Carbon::now()->subDays(3),
                'deskripsi'           => 'Pembayaran sparepart',
                'reference_no'        => 'BANK-INV-004',
                'amount'              => 750000,
                'currency'            => 'IDR',
                'status_rekonsiliasi' => 'matched',
                'invoice_id'          => null,
            ],

            [
                'tanggal'             => Carbon::now(),
                'deskripsi'           => 'Pemasukan rental harian',
                'reference_no'        => 'BANK-INV-005',
                'amount'              => 1200000,
                'currency'            => 'IDR',
                'status_rekonsiliasi' => 'Pending',
                'invoice_id'          => null,
            ],

        ];

        foreach ($data as $item) {
            RekonsiliasiBank::create($item);
        }
    }
}