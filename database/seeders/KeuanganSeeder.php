<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Keuangan;
use Carbon\Carbon;

class KeuanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [

            [
                'tanggal'     => Carbon::now()->subDays(10),
                'reference'   => 'INV-001',
                'user_id'     => 1,
                'kategori'    => 'Pemasukan',
                'metode'      => 'Cash',
                'keterangan'  => 'Pembayaran rental mobil',
                'pemasukan'   => 1500000,
                'pengeluaran' => 0,
                'saldo'       => 1500000,
            ],

            [
                'tanggal'     => Carbon::now()->subDays(8),
                'reference'   => 'EXP-001',
                'user_id'     => 1,
                'kategori'    => 'Pengeluaran',
                'metode'      => 'Transfer',
                'keterangan'  => 'Service kendaraan',
                'pemasukan'   => 0,
                'pengeluaran' => 500000,
                'saldo'       => 1000000,
            ],

            [
                'tanggal'     => Carbon::now()->subDays(5),
                'reference'   => 'INV-002',
                'user_id'     => 1,
                'kategori'    => 'Pemasukan',
                'metode'      => 'Transfer',
                'keterangan'  => 'Rental harian',
                'pemasukan'   => 2000000,
                'pengeluaran' => 0,
                'saldo'       => 3000000,
            ],

            [
                'tanggal'     => Carbon::now()->subDays(3),
                'reference'   => 'EXP-002',
                'user_id'     => 1,
                'kategori'    => 'Pengeluaran',
                'metode'      => 'Cash',
                'keterangan'  => 'Beli sparepart',
                'pemasukan'   => 0,
                'pengeluaran' => 750000,
                'saldo'       => 2250000,
            ],

            [
                'tanggal'     => Carbon::now(),
                'reference'   => 'INV-003',
                'user_id'     => 1,
                'kategori'    => 'Pemasukan',
                'metode'      => 'Cash',
                'keterangan'  => 'DP rental kendaraan',
                'pemasukan'   => 1000000,
                'pengeluaran' => 0,
                'saldo'       => 3250000,
            ],

        ];

        foreach ($data as $item) {
            Keuangan::create($item);
        }
    }
}