<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceDetail;

class ServiceDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [

            [
                'kendaraan_id'    => 1,
                'tanggal_service' => '2026-01-10',
                'kilometer'       => 25000,
                'status'          => 'Layak',
                'biaya'           => 350000,
                'keterangan'      => 'Ganti oli dan filter oli',
                'bukti'           => null,
            ],

            [
                'kendaraan_id'    => 1,
                'tanggal_service' => '2026-02-15',
                'kilometer'       => 28000,
                'status'          => 'Layak',
                'biaya'           => 500000,
                'keterangan'      => 'Ganti kampas rem depan',
                'bukti'           => null,
            ],

            [
                'kendaraan_id'    => 2,
                'tanggal_service' => '2026-03-05',
                'kilometer'       => 42000,
                'status'          => 'Layak',
                'biaya'           => 600000,
                'keterangan'      => 'Isi freon dan service AC',
                'bukti'           => null,
            ],

            [
                'kendaraan_id'    => 3,
                'tanggal_service' => '2026-03-20',
                'kilometer'       => 55000,
                'status'          => 'Tidak Layak',
                'biaya'           => 1800000,
                'keterangan'      => 'Ganti 2 ban depan',
                'bukti'           => null,
            ],          
        ];

        foreach ($data as $item) {
            ServiceDetail::create($item);
        }
    }
}