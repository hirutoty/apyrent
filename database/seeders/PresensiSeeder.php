<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Presensi;
use Carbon\Carbon;

class PresensiSeeder extends Seeder
{
    public function run(): void
    {
        $pegawai = [
            'Budi Santoso',    'Dewi Kusuma',     'Rini Apriani',
            'Eko Prasetyo',    'Hendra Gunawan',  'Rizky Fadillah',
            'Yusuf Hidayat',   'Linda Permata',   'Wahyu Nugroho',
            'Fitri Handayani', 'Dody Kurniawan',  'Teguh Santosa',
        ];

        $metode   = ['Fingerprint', 'Face ID', 'GPS', 'Manual'];
        $lokasi   = ['Kantor Jakarta', 'Kantor Surabaya', 'WFH', 'Lapangan'];
        $statusList = ['Hadir', 'Hadir', 'Hadir', 'Hadir', 'Terlambat', 'Izin', 'Alpa'];

        // Generate presensi 30 hari terakhir untuk setiap pegawai
        for ($day = 29; $day >= 0; $day--) {
            $tanggal = Carbon::now()->subDays($day)->toDateString();
            $dayOfWeek = Carbon::parse($tanggal)->dayOfWeek;

            // Skip Sabtu (6) dan Minggu (0)
            if (in_array($dayOfWeek, [0, 6])) continue;

            foreach ($pegawai as $p) {
                $status   = $statusList[array_rand($statusList)];
                $jamMasuk = $status === 'Terlambat'
                    ? sprintf('%02d:%02d:00', rand(8, 9), rand(10, 59))
                    : sprintf('%02d:%02d:00', rand(7, 8), rand(0, 59));
                $jamPulang = sprintf('%02d:%02d:00', rand(17, 18), rand(0, 59));

                Presensi::create([
                    'nama_pegawai'    => $p,
                    'tanggal'         => $tanggal,
                    'jam_masuk'       => in_array($status, ['Alpa', 'Izin']) ? '00:00:00' : $jamMasuk,
                    'jam_pulang'      => in_array($status, ['Alpa', 'Izin']) ? '00:00:00' : $jamPulang,
                    'metode_presensi' => $metode[array_rand($metode)],
                    'lokasi_presensi' => $lokasi[array_rand($lokasi)],
                    'status'          => $status,
                ]);
            }
        }
    }
}
