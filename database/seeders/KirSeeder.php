<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kir;
use Carbon\Carbon;

class KirSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 50; $i++) {
            Kir::create([
                'kendaraan_id' => (($i - 1) % 50) + 1,
                'no_uji'       => 'KIR-' . date('Y') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'masa_berlaku' => Carbon::now()->addDays(rand(-60, 730)),
                'biaya'        => rand(1, 10) * 50000,
                'image'        => null,
            ]);
        }
    }
}
