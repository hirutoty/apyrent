<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kir;
use Carbon\Carbon;

class KirSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [

            [
                'kendaraan_id' => 1,
                'no_uji'       => 'KIR-2026-001',
                'masa_berlaku' => Carbon::now()->addMonths(6),
                'biaya' => 200000,
                'image'        => null,
            ],

            [
                'kendaraan_id' => 2,
                'no_uji'       => 'KIR-2026-002',
                'masa_berlaku' => Carbon::now()->addMonths(4),
                'biaya' => 500000,
                'image'        => null,
            ],

            [
                'kendaraan_id' => 3,
                'no_uji'       => 'KIR-2026-003',
                'masa_berlaku' => Carbon::now()->subDays(10),
                'biaya' => 130000,
                'image'        => null,
            ],

            [
                'kendaraan_id' => 1,
                'no_uji'       => 'KIR-2026-004',
                'masa_berlaku' => Carbon::now()->addYear(),
                'biaya' => 4500000,
                'image'        => null,
            ],

            

        ];

        foreach ($data as $item) {
            Kir::create($item);
        }
    }
}