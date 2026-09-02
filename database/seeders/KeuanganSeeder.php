<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Keuangan;
use Carbon\Carbon;

class KeuanganSeeder extends Seeder
{
    public function run(): void
    {
        $kategoriPemasukan  = ['Rental', 'Deposit', 'Denda', 'Lain-lain', 'Pelunasan'];
        $kategoriPengeluaran = ['Servis', 'Bahan Bakar', 'Pajak', 'Asuransi', 'GPS', 'Gaji', 'Operasional', 'Spare Part'];
        $metode = ['cash', 'transfer'];

        $year = 2026;
        $saldo = 10000000; // Saldo awal 10 juta

        // Generate 120 transaksi: 10 per bulan (Jan-Des 2026)
        for ($month = 1; $month <= 12; $month++) {
            $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
            
            for ($j = 1; $j <= 10; $j++) {
                $day = rand(1, $daysInMonth);
                $tanggal = Carbon::create($year, $month, $day)
                    ->setTime(rand(8, 17), rand(0, 59), rand(0, 59));

                $isPemasukan = ($j % 3 !== 0); // 2/3 pemasukan, 1/3 pengeluaran
                $nominal     = rand(200, 5000) * 1000;

                if ($isPemasukan) {
                    $saldo      += $nominal;
                    $pemasukan   = $nominal;
                    $pengeluaran = 0;
                    $kategori    = $kategoriPemasukan[array_rand($kategoriPemasukan)];
                    $keterangan  = 'Penerimaan ' . $kategori . ' bulan ' . $month;
                    $ref         = 'INV-' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . str_pad($j, 2, '0', STR_PAD_LEFT);
                } else {
                    $saldo      -= $nominal;
                    $pemasukan   = 0;
                    $pengeluaran = $nominal;
                    $kategori    = $kategoriPengeluaran[array_rand($kategoriPengeluaran)];
                    $keterangan  = 'Pengeluaran ' . $kategori . ' bulan ' . $month;
                    $ref         = 'EXP-' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . str_pad($j, 2, '0', STR_PAD_LEFT);
                }

                Keuangan::create([
                    'tanggal'     => $tanggal,
                    'reference'   => $ref,
                    'user_id'     => 1,
                    'kategori'    => $kategori,
                    'metode'      => $metode[array_rand($metode)],
                    'keterangan'  => $keterangan,
                    'pemasukan'   => $pemasukan,
                    'pengeluaran' => $pengeluaran,
                    'saldo'       => max(0, $saldo),
                ]);
            }
        }
    }
}
