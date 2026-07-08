<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AsuransiKendaraan;
use Carbon\Carbon;

class AsuransiKendaraanSeeder extends Seeder
{
    public function run(): void
    {
        $durasiOptions = [3, 6, 12, 24];

        for ($i = 1; $i <= 50; $i++) {
            $durasi     = $durasiOptions[($i - 1) % count($durasiOptions)];
            $tglMulai   = Carbon::now()->subMonths(rand(0, 20));
            $tglBerakhir = (clone $tglMulai)->addMonths($durasi);
            $status     = $tglBerakhir->isFuture() ? 'aktif' : 'expired';

            AsuransiKendaraan::create([
                'kendaraan_id'      => (($i - 1) % 50) + 1,
                'asuransi_id'       => (($i - 1) % 3) + 1,
                'jenis_asuransi_id' => (($i - 1) % 3) + 1,
                'status_kendaraan'  => $status,
                'tgl_mulai'         => $tglMulai,
                'tgl_berakhir'      => $tglBerakhir,
                'durasi_bulan'      => $durasi,
                'biaya'             => rand(5, 50) * 500000,
                'bukti_bayar'       => null,
            ]);
        }
    }
}
