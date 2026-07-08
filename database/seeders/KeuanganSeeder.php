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

        $saldo = 0;

        for ($i = 1; $i <= 50; $i++) {
            $isPemasukan = ($i % 3 !== 0); // 2/3 pemasukan, 1/3 pengeluaran
            $nominal     = rand(200, 5000) * 1000;

            if ($isPemasukan) {
                $saldo      += $nominal;
                $pemasukan   = $nominal;
                $pengeluaran = 0;
                $kategori    = $kategoriPemasukan[($i - 1) % count($kategoriPemasukan)];
                $keterangan  = 'Penerimaan ' . $kategori . ' ke-' . $i;
                $ref         = 'INV-' . str_pad($i, 3, '0', STR_PAD_LEFT);
            } else {
                $saldo      -= $nominal;
                $pemasukan   = 0;
                $pengeluaran = $nominal;
                $kategori    = $kategoriPengeluaran[($i - 1) % count($kategoriPengeluaran)];
                $keterangan  = 'Pengeluaran ' . $kategori . ' ke-' . $i;
                $ref         = 'EXP-' . str_pad($i, 3, '0', STR_PAD_LEFT);
            }

            Keuangan::create([
                'tanggal'     => Carbon::now()->subDays(rand(1, 180)),
                'reference'   => $ref,
                'user_id'     => 1,
                'kategori'    => $kategori,
                'metode'      => $metode[($i - 1) % count($metode)],
                'keterangan'  => $keterangan,
                'pemasukan'   => $pemasukan,
                'pengeluaran' => $pengeluaran,
                'saldo'       => max(0, $saldo),
            ]);
        }
    }
}
