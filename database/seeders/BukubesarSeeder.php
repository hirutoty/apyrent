<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bukubesar;
use Carbon\Carbon;

class BukubesarSeeder extends Seeder
{
    public function run(): void
    {
        Bukubesar::create([
            'kode_jurnal' => 'JRNL-001',
            'transaksi' => 'Pemasukan Rental Mobil',
            'kategori' => 'pemasukan',
            'tanggal' => Carbon::now()->subDays(5),
            'debit' => 1500000,
            'kredit' => 0,
            'saldo' => 1500000,
            'aktivitas' => 'rental',
            'keterangan' => 'Pembayaran rental mobil dari customer A',
        ]);

        Bukubesar::create([
            'kode_jurnal' => 'JRNL-002',
            'transaksi' => 'Pembelian Service Kendaraan',
            'kategori' => 'pengeluaran',
            'tanggal' => Carbon::now()->subDays(3),
            'debit' => 0,
            'kredit' => 500000,
            'saldo' => 1000000,
            'aktivitas' => 'service',
            'keterangan' => 'Biaya servis berkala kendaraan',
        ]);

        Bukubesar::create([
            'kode_jurnal' => 'JRNL-003',
            'transaksi' => 'Pajak Kendaraan',
            'kategori' => 'pengeluaran',
            'tanggal' => Carbon::now()->subDays(1),
            'debit' => 0,
            'kredit' => 200000,
            'saldo' => 800000,
            'aktivitas' => 'pajak',
            'keterangan' => 'Pembayaran pajak kendaraan tahunan',
        ]);
    }
}