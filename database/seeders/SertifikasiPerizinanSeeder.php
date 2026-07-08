<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SertifikasiPerizinan;

class SertifikasiPerizinanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['jenis' => 'ISO 9001:2015', 'nomor' => 'ISO9001-2401', 'instansi' => 'Sucofindo', 'berlaku_hingga' => '2027-02-20', 'status' => 'Aktif'],
            ['jenis' => 'NIB', 'nomor' => '1234567890123', 'instansi' => 'OSS / BKPM', 'berlaku_hingga' => 'Tidak Kadaluarsa', 'status' => 'Aktif'],
            ['jenis' => 'STNK Kendaraan Operasional', 'nomor' => 'STNK-2024-001', 'instansi' => 'SAMSAT', 'berlaku_hingga' => '2025-04-30', 'status' => 'Aktif'],
            ['jenis' => 'Sertifikat Halal', 'nomor' => 'MUI-2023-0045', 'instansi' => 'MUI', 'berlaku_hingga' => '2025-08-15', 'status' => 'Aktif'],
            ['jenis' => 'Izin Lingkungan', 'nomor' => 'AMDAL-2022-078', 'instansi' => 'KLHK', 'berlaku_hingga' => '2024-12-31', 'status' => 'Kadaluarsa'],
        ];
        foreach ($data as $item) {
            SertifikasiPerizinan::create($item);
        }
    }
}
