<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceHistory;
use Carbon\Carbon;

class ServiceHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Keluhan dan biaya berdasarkan jenis kerusakan / perawatan
        $keluhanData = [
            ['keluhan' => 'Ganti oli mesin dan filter oli',                  'biaya' => 350000],
            ['keluhan' => 'Oli mesin sudah hitam, perlu segera diganti',     'biaya' => 320000],
            ['keluhan' => 'Rem depan dan belakang berbunyi saat diinjak',    'biaya' => 750000],
            ['keluhan' => 'Kampas rem aus, perlu penggantian',               'biaya' => 680000],
            ['keluhan' => 'AC tidak dingin, freon habis',                    'biaya' => 600000],
            ['keluhan' => 'Kompresor AC bermasalah',                         'biaya' => 2800000],
            ['keluhan' => 'Ban depan kiri bocor dan aus',                    'biaya' => 950000],
            ['keluhan' => 'Ganti 4 ban baru karena sudah halus',             'biaya' => 4800000],
            ['keluhan' => 'Spooring dan balancing ban',                      'biaya' => 280000],
            ['keluhan' => 'Mesin getar saat idle, busi kotor',               'biaya' => 450000],
            ['keluhan' => 'Ganti busi semua silinder',                       'biaya' => 380000],
            ['keluhan' => 'Aki lemah, mesin susah dinyalakan',               'biaya' => 1200000],
            ['keluhan' => 'Ganti aki baru',                                  'biaya' => 1350000],
            ['keluhan' => 'Lampu depan redup, perlu ganti bohlam',           'biaya' => 250000],
            ['keluhan' => 'Lampu belakang mati',                             'biaya' => 180000],
            ['keluhan' => 'Wiper tidak berfungsi optimal',                   'biaya' => 320000],
            ['keluhan' => 'Transmisi kasar saat pindah gigi',                'biaya' => 1800000],
            ['keluhan' => 'Oli transmisi sudah kotor',                       'biaya' => 550000],
            ['keluhan' => 'Kopling selip saat tanjakan',                     'biaya' => 2200000],
            ['keluhan' => 'Ganti kopling set',                               'biaya' => 3500000],
            ['keluhan' => 'Suspensi depan berbunyi saat melewati polisi tidur', 'biaya' => 1600000],
            ['keluhan' => 'Shockbreaker bocor, perlu penggantian',           'biaya' => 2400000],
            ['keluhan' => 'Service berkala 10.000 km',                       'biaya' => 850000],
            ['keluhan' => 'Service berkala 20.000 km',                       'biaya' => 1200000],
            ['keluhan' => 'Service berkala 40.000 km (tune up)',             'biaya' => 2500000],
            ['keluhan' => 'Radiator bocor, mesin overheat',                  'biaya' => 3200000],
            ['keluhan' => 'Ganti air radiator dan thermostat',               'biaya' => 450000],
            ['keluhan' => 'Knalpot berkarat dan bocor',                      'biaya' => 1500000],
            ['keluhan' => 'Timing belt sudah retak perlu diganti',           'biaya' => 2800000],
            ['keluhan' => 'Ganti v-belt dan tensioner',                      'biaya' => 950000],
            ['keluhan' => 'Power steering berat dan bocor',                  'biaya' => 1700000],
            ['keluhan' => 'Ganti power steering fluid',                      'biaya' => 300000],
            ['keluhan' => 'Fuel pump lemah, mesin sering mati mendadak',     'biaya' => 1900000],
            ['keluhan' => 'Filter bensin kotor',                             'biaya' => 280000],
            ['keluhan' => 'Kaca depan retak akibat batu',                    'biaya' => 2100000],
            ['keluhan' => 'Ganti filter AC kabin',                           'biaya' => 350000],
            ['keluhan' => 'Sistem kelistrikan bermasalah, sekring putus',    'biaya' => 650000],
            ['keluhan' => 'Alternator tidak mengisi, aki cepat habis',       'biaya' => 2600000],
            ['keluhan' => 'Cat bodi terkelupas, perlu touch up',             'biaya' => 1400000],
            ['keluhan' => 'Service tune up lengkap',                         'biaya' => 3000000],
        ];

        $statusOptions       = ['selesai', 'selesai', 'selesai', 'proses', 'limit'];
        $statusPengeluaran   = ['stabil', 'stabil', 'stabil', 'overservice'];
        $statusApproval      = ['approved', 'approved', 'approved', 'pending', 'rejected', null, null];

        // Rentang tanggal: Januari 2025 s/d Desember 2026
        $startDate = Carbon::create(2025, 1, 1);
        $endDate   = Carbon::create(2026, 12, 31);

        $data = [];

        // Kendaraan 1-10: banyak riwayat (kendaraan aktif)
        foreach (range(1, 10) as $kendaraanId) {
            // 8–12 service per kendaraan
            $jumlah = rand(8, 12);
            for ($j = 0; $j < $jumlah; $j++) {
                $keluhan     = $keluhanData[array_rand($keluhanData)];
                $tanggal     = Carbon::createFromTimestamp(
                    rand($startDate->timestamp, $endDate->timestamp)
                )->format('Y-m-d');
                $isRequest   = (bool) rand(0, 1);
                $approval    = $statusApproval[array_rand($statusApproval)];
                $biayaVariasi = $keluhan['biaya'] + rand(-50000, 200000);

                $data[] = [
                    'kendaraan_id'      => $kendaraanId,
                    'keluhan'           => $keluhan['keluhan'],
                    'kilometer'         => rand(10000, 130000),
                    'total_biaya'       => max(100000, $biayaVariasi),
                    'status'            => $statusOptions[array_rand($statusOptions)],
                    'maks_bulanan'      => rand(0, 1) ? rand(1, 5) * 1000000 : 0,
                    'biaya_tahunan'     => rand(0, 1) ? rand(5, 20) * 1000000 : 0,
                    'status_pengeluaran' => $statusPengeluaran[array_rand($statusPengeluaran)],
                    'bukti_pembayaran'  => null,
                    'status_approval'   => $approval,
                    'approval_by'       => $approval === 'approved' ? 1 : null,
                    'approval_at'       => $approval === 'approved'
                        ? Carbon::parse($tanggal)->addDays(rand(1, 3))
                        : null,
                    'is_request'        => $isRequest,
                    'tanggal_service'   => $tanggal,
                ];
            }
        }

        // Kendaraan 11-30: riwayat menengah (4–7 service)
        foreach (range(11, 30) as $kendaraanId) {
            $jumlah = rand(4, 7);
            for ($j = 0; $j < $jumlah; $j++) {
                $keluhan    = $keluhanData[array_rand($keluhanData)];
                $tanggal    = Carbon::createFromTimestamp(
                    rand($startDate->timestamp, $endDate->timestamp)
                )->format('Y-m-d');
                $isRequest  = (bool) rand(0, 1);
                $approval   = $statusApproval[array_rand($statusApproval)];
                $biayaVariasi = $keluhan['biaya'] + rand(-30000, 150000);

                $data[] = [
                    'kendaraan_id'      => $kendaraanId,
                    'keluhan'           => $keluhan['keluhan'],
                    'kilometer'         => rand(5000, 100000),
                    'total_biaya'       => max(100000, $biayaVariasi),
                    'status'            => $statusOptions[array_rand($statusOptions)],
                    'maks_bulanan'      => rand(0, 1) ? rand(1, 4) * 1000000 : 0,
                    'biaya_tahunan'     => rand(0, 1) ? rand(4, 15) * 1000000 : 0,
                    'status_pengeluaran' => $statusPengeluaran[array_rand($statusPengeluaran)],
                    'bukti_pembayaran'  => null,
                    'status_approval'   => $approval,
                    'approval_by'       => $approval === 'approved' ? 1 : null,
                    'approval_at'       => $approval === 'approved'
                        ? Carbon::parse($tanggal)->addDays(rand(1, 3))
                        : null,
                    'is_request'        => $isRequest,
                    'tanggal_service'   => $tanggal,
                ];
            }
        }

        // Kendaraan 31-50: sedikit riwayat (1–3 service)
        foreach (range(31, 50) as $kendaraanId) {
            $jumlah = rand(1, 3);
            for ($j = 0; $j < $jumlah; $j++) {
                $keluhan    = $keluhanData[array_rand($keluhanData)];
                $tanggal    = Carbon::createFromTimestamp(
                    rand($startDate->timestamp, $endDate->timestamp)
                )->format('Y-m-d');
                $isRequest  = (bool) rand(0, 1);
                $approval   = $statusApproval[array_rand($statusApproval)];
                $biayaVariasi = $keluhan['biaya'] + rand(-20000, 100000);

                $data[] = [
                    'kendaraan_id'      => $kendaraanId,
                    'keluhan'           => $keluhan['keluhan'],
                    'kilometer'         => rand(3000, 80000),
                    'total_biaya'       => max(100000, $biayaVariasi),
                    'status'            => $statusOptions[array_rand($statusOptions)],
                    'maks_bulanan'      => 0,
                    'biaya_tahunan'     => 0,
                    'status_pengeluaran' => $statusPengeluaran[array_rand($statusPengeluaran)],
                    'bukti_pembayaran'  => null,
                    'status_approval'   => $approval,
                    'approval_by'       => $approval === 'approved' ? 1 : null,
                    'approval_at'       => $approval === 'approved'
                        ? Carbon::parse($tanggal)->addDays(rand(1, 3))
                        : null,
                    'is_request'        => $isRequest,
                    'tanggal_service'   => $tanggal,
                ];
            }
        }

        foreach ($data as $item) {
            ServiceHistory::create($item);
        }
    }
}