<?php

namespace Database\Seeders;

use App\Models\Loyalty;
use Illuminate\Database\Seeder;

class LoyaltySeeder extends Seeder
{
    public function run(): void
    {
        Loyalty::create([
            'id_program' => 'LYL001',
            'nama_program' => 'APY Points',
            'jenis_reward' => 'Poin',
            'akumulasi_poin' => '1 poin per 10.000',
            'konversi_poin' => '100 poin = Rp 50.000',
            'periode_mulai' => '2026-01-01',
            'periode_akhir' => '2026-12-31',
            'status' => 'Aktif',
        ]);

        Loyalty::create([
            'id_program' => 'LYL002',
            'nama_program' => 'Free Day Program',
            'jenis_reward' => 'Hari Gratis',
            'akumulasi_poin' => '10 hari rental = 1 hari gratis',
            'konversi_poin' => '1 hari gratis per periode',
            'periode_mulai' => '2026-07-01',
            'periode_akhir' => '2026-12-31',
            'status' => 'Aktif',
        ]);
    }
}
