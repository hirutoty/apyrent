<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rental;
use Carbon\Carbon;

class RentalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [

            [
                'user_id'                => 1,
                'kendaraan_id'           => 1,
                'member_id'              => 1,

                'tanggal_mulai'          => Carbon::now()->subDays(3),
                'tanggal_selesai'        => Carbon::now()->addDays(2),

                'durasi_jam'             => 0,
                'durasi_hari'            => 5,

                'biaya_dasar'            => 1750000,
                'biaya_tambahan_total'   => 200000,
                'total_biaya'            => 1950000,

                
                'status'                 => 'aktif',
            ],

            [
                'user_id'                => 1,
                'kendaraan_id'           => 2,
                'member_id'              => 2,

                'tanggal_mulai'          => Carbon::now()->subDays(10),
                'tanggal_selesai'        => Carbon::now()->subDays(5),

                'durasi_jam'             => 6,
                'durasi_hari'            => 2,

                'biaya_dasar'            => 1000000,
                'biaya_tambahan_total'   => 150000,
                'total_biaya'            => 1150000,

                
                'status'                 => 'selesai',
            ],

            [
                'user_id'                => 1,
                'kendaraan_id'           => 3,
                'member_id'              => 3,

                'tanggal_mulai'          => Carbon::now()->subDay(),
                'tanggal_selesai'        => Carbon::now()->addDays(1),

                'durasi_jam'             => 4,
                'durasi_hari'            => 1,

                'biaya_dasar'            => 550000,
                'biaya_tambahan_total'   => 50000,
                'total_biaya'            => 600000,

                
                'status'                 => 'aktif',
            ],

            

            [
                'user_id'                => 1,
                'kendaraan_id'           => 2,
                'member_id'              => 5,

                'tanggal_mulai'          => Carbon::now(),
                'tanggal_selesai'        => Carbon::now()->addDays(7),

                'durasi_jam'             => 0,
                'durasi_hari'            => 7,

                'biaya_dasar'            => 4200000,
                'biaya_tambahan_total'   => 500000,
                'total_biaya'            => 4700000,

                
                'status'                 => 'booking',
            ],

        ];

        foreach ($data as $item) {
            Rental::create($item);
        }
    }
}