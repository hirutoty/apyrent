<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceDetail;
use Carbon\Carbon;

class ServiceDetailSeeder extends Seeder
{
    public function run(): void
    {
        $keluhan = [
            'Ganti oli mesin', 'Tune up', 'Ganti kampas rem', 'Servis AC', 'Ganti ban',
            'Overhaul mesin', 'Ganti aki', 'Servis transmisi', 'Ganti filter udara', 'Perbaikan body',
            'Ganti busi', 'Servis suspensi', 'Ganti timing belt', 'Kalibrasi lampu', 'Servis power steering',
            'Ganti knalpot', 'Perbaikan sistem pendingin', 'Ganti kopling', 'Servis rem tangan', 'Ganti wiper',
        ];

        for ($i = 1; $i <= 50; $i++) {
            $kmSekarang = rand(5000, 120000);

            ServiceDetail::create([
                'kendaraan_id'    => (($i - 1) % 50) + 1,
                'tanggal_service' => Carbon::now()->subDays(rand(1, 365)),
                'kilometer'       => $kmSekarang,
                'status'          => ($i % 4 === 0) ? 'Tidak Layak' : 'Layak',
                'biaya'           => rand(2, 30) * 50000,
                'keterangan'      => $keluhan[($i - 1) % count($keluhan)],
                'bukti'           => null,
            ]);
        }
    }
}
