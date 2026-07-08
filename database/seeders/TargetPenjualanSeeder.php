<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TargetPenjualanSeeder extends Seeder
{
    public function run(): void
    {
        $sales = ['Andi', 'Budi', 'Cici', 'Dani'];
        $bulan = ['2026-01', '2026-02', '2026-03', '2026-04', '2026-05', '2026-06'];

        foreach ($bulan as $b) {
            foreach ($sales as $s) {
                $target     = rand(20, 60) * 1000000;
                $pencapaian = rand(15, 65) * 1000000;
                DB::table('target_penjualans')->insert([
                    'nama_sales'       => $s,
                    'bulan'            => $b,
                    'target_penjualan' => $target,
                    'pencapaian'       => $pencapaian,
                    'keterangan'       => $pencapaian >= $target ? 'Target tercapai' : 'Belum mencapai target',
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }
        }
    }
}
