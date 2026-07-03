<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HutangVendor;
use Carbon\Carbon;

class HutangVendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [

            [
                'nama_vendor' => 'PT Sinar Abadi',
                'kategori'    => 'Sparepart',
                'nominal'     => 5000000,
                'dibayar'     => 2000000,
                'sisa'        => 3000000,
                'jatuh_tempo' => Carbon::now()->addDays(10),
                'status'      => 'belum_lunas',
                'keterangan'  => 'Pembelian sparepart mesin',
            ],

            [
                'nama_vendor' => 'CV Mitra Jaya',
                'kategori'    => 'Service',
                'nominal'     => 2500000,
                'dibayar'     => 2500000,
                'sisa'        => 0,
                'jatuh_tempo' => Carbon::now()->subDays(5),
                'status'      => 'lunas',
                'keterangan'  => 'Service kendaraan fleet',
            ],

            [
                'nama_vendor' => 'PT Otomotif Nusantara',
                'kategori'    => 'Aksesoris',
                'nominal'     => 1200000,
                'dibayar'     => 500000,
                'sisa'        => 700000,
                'jatuh_tempo' => Carbon::now()->addDays(3),
                'status'      => 'belum_lunas',
                'keterangan'  => 'Pembelian aksesoris mobil',
            ],

            [
                'nama_vendor' => 'UD Jaya Mandiri',
                'kategori'    => 'Ban',
                'nominal'     => 3000000,
                'dibayar'     => 1000000,
                'sisa'        => 2000000,
                'jatuh_tempo' => Carbon::now()->addDays(15),
                'status'      => 'belum_lunas',
                'keterangan'  => 'Pembelian ban kendaraan',
            ],

            [
                'nama_vendor' => 'PT Diesel Prima',
                'kategori'    => 'Mesin',
                'nominal'     => 8000000,
                'dibayar'     => 8000000,
                'sisa'        => 0,
                'jatuh_tempo' => Carbon::now()->subDays(2),
                'status'      => 'lunas',
                'keterangan'  => 'Perbaikan mesin besar',
            ],

        ];

        foreach ($data as $item) {
            HutangVendor::create($item);
        }
    }
}