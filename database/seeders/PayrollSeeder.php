<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payroll;

class PayrollSeeder extends Seeder
{
    public function run(): void
    {
        // [nama, gaji_pokok, tunjangan, thr, bpjs, pph21]
        $data = [
            ['Budi Santoso',       25000000, 5000000, 25000000, 500000, 2500000],
            ['Siti Rahayu',        20000000, 4000000, 20000000, 400000, 2000000],
            ['Agus Wibowo',        20000000, 4000000, 20000000, 400000, 2000000],
            ['Dewi Kusuma',        12000000, 2000000, 12000000, 240000,  600000],
            ['Rini Apriani',        6000000, 1000000,  6000000, 120000,  150000],
            ['Eko Prasetyo',        5500000,  800000,  5500000, 110000,  120000],
            ['Hendra Gunawan',     13000000, 2500000, 13000000, 260000,  750000],
            ['Rizky Fadillah',      7000000, 1200000,  7000000, 140000,  200000],
            ['Yusuf Hidayat',       5500000,  800000,  5500000, 110000,  120000],
            ['Linda Permata',      13000000, 2500000, 13000000, 260000,  750000],
            ['Wahyu Nugroho',       7500000, 1200000,  7500000, 150000,  220000],
            ['Fitri Handayani',     6500000, 1000000,  6500000, 130000,  170000],
            ['Dody Kurniawan',     11000000, 2000000, 11000000, 220000,  550000],
            ['Teguh Santosa',       8000000, 1500000,  8000000, 160000,  280000],
            ['Arif Budiman',        5500000,  800000,  5500000, 110000,  120000],
        ];

        foreach ($data as [$nama, $gaji, $tunjangan, $thr, $bpjs, $pph21]) {
            $total = $gaji + $tunjangan - $bpjs - $pph21;
            Payroll::updateOrCreate(
                ['nama_pegawai' => $nama],
                [
                    'nama_pegawai' => $nama,
                    'gaji_pokok'   => $gaji,
                    'tunjangan'    => $tunjangan,
                    'thr'          => $thr,
                    'bpjs'         => $bpjs,
                    'pph21'        => $pph21,
                    'total_gaji'   => $total,
                    'slip_gaji'    => null,
                ]
            );
        }
    }
}
