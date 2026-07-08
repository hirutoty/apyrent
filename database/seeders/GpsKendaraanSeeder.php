<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GpsKendaraan;
use Carbon\Carbon;

class GpsKendaraanSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['OBD', 'Hardwire', 'Magnetic', '4G LTE', 'Solar'];

        for ($i = 1; $i <= 50; $i++) {
            $durasibulan   = rand(6, 24);
            $tanggalPasang = Carbon::now()->subMonths(rand(1, 18));
            $tanggalHabis  = (clone $tanggalPasang)->addMonths($durasibulan);
            $statusSewa    = $tanggalHabis->isFuture() ? 'aktif' : 'habis';

            GpsKendaraan::create([
                'kendaraan_id'  => (($i - 1) % 50) + 1,
                'gps_id'        => (($i - 1) % 10) + 1,
                'type'          => $types[($i - 1) % count($types)],
                'status_gps'    => $statusSewa === 'aktif' ? 'aktif' : 'nonaktif',
                'tanggal_pasang' => $tanggalPasang,
                'tanggal_habis'  => $tanggalHabis,
                'biaya_sewa'    => rand(1, 5) * 100000,
                'durasi_bulan'  => $durasibulan,
                'status_sewa'   => $statusSewa,
                'bukti_bayar'   => null,
            ]);
        }
    }
}
