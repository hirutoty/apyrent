<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceHistory;
use Carbon\Carbon;

class ServiceHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [

            [
                'kendaraan_id'    => 1,
                'keluhan'         => 'Oli mesin sudah hitam dan rem berbunyi',
                'kilometer'       => 45000,
                'total_biaya'     => 850000,
                'status'          => 'selesai',
                'tanggal_service' => Carbon::now()->subDays(20),
            ],

            [
                'kendaraan_id'    => 2,
                'keluhan'         => 'AC tidak dingin',
                'kilometer'       => 52000,
                'total_biaya'     => 600000,
                'status'          => 'proses',
                'tanggal_service' => Carbon::now()->subDays(10),
            ],

            [
                'kendaraan_id'    => 3,
                'keluhan'         => 'Ban depan aus',
                'kilometer'       => 70000,
                'total_biaya'     => 1800000,
                'status'          => 'selesai',
                'tanggal_service' => Carbon::now()->subDays(30),
            ],

            [
                'kendaraan_id'    => 1,
                'keluhan'         => 'Mesin getar saat idle',
                'kilometer'       => 47000,
                'total_biaya'     => 500000,
                'status'          => 'proses',
                'tanggal_service' => Carbon::now()->subDays(5),
            ],

            [
                'kendaraan_id'    => 2,
                'keluhan'         => 'Ganti aki',
                'kilometer'       => 55000,
                'total_biaya'     => 1200000,
                'status'          => 'selesai',
                'tanggal_service' => Carbon::now()->subDays(15),
            ],

        ];

        foreach ($data as $item) {
            ServiceHistory::create($item);
        }
    }
}