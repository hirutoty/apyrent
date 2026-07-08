<?php

namespace Database\Seeders;

use App\Models\Afiliasi;
use Illuminate\Database\Seeder;

class AfiliasiSeeder extends Seeder
{
    public function run(): void
    {
        Afiliasi::create([
            'id_program' => 'AFI001',
            'nama_program' => 'Referral Teman',
            'kode_referral' => 'REF-APY001',
            'diskon_referral' => 50000,
            'bonus_pengajak' => 'Rp 75.000 kredit',
            'batas_waktu' => '2026-12-31',
            'status' => 'Aktif',
        ]);

        Afiliasi::create([
            'id_program' => 'AFI002',
            'nama_program' => 'Corporate Partner',
            'kode_referral' => 'REF-CORP001',
            'diskon_referral' => 100000,
            'bonus_pengajak' => 'Komisi 5%',
            'batas_waktu' => '2026-12-31',
            'status' => 'Aktif',
        ]);
    }
}
