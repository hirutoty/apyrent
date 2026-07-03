<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisAsuransi;

class JenisAsuransiSeeder extends Seeder
{
    public function run(): void
    {
        JenisAsuransi::create([
            'nama_jenis' => 'All Risk',
            'keterangan' => 'Menanggung kerusakan ringan dan berat',
        ]);

        JenisAsuransi::create([
            'nama_jenis' => 'TLO',
            'keterangan' => 'Total Loss Only',
        ]);

        JenisAsuransi::create([
            'nama_jenis' => 'Comprehensive',
            'keterangan' => 'Perlindungan menyeluruh kendaraan',
        ]);
    }
}