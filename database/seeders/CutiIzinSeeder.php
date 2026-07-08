<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CutiIzin;
use Carbon\Carbon;

class CutiIzinSeeder extends Seeder
{
    public function run(): void
    {
        $pegawai = [
            'Rini Apriani',    'Eko Prasetyo',    'Rizky Fadillah',
            'Yusuf Hidayat',   'Wahyu Nugroho',   'Fitri Handayani',
            'Teguh Santosa',   'Arif Budiman',    'Dewi Kusuma',
            'Linda Permata',   'Hendra Gunawan',  'Dody Kurniawan',
        ];

        $jenisCuti = [
            'Cuti Tahunan', 'Cuti Sakit', 'Cuti Melahirkan',
            'Izin Pribadi', 'Cuti Bersama',
        ];

        $alasan = [
            'Keperluan keluarga',
            'Pemulihan kesehatan',
            'Acara pernikahan',
            'Mengurus administrasi',
            'Liburan keluarga',
            'Cuti bersama hari raya',
            'Rawat inap di rumah sakit',
            'Keperluan mendesak pribadi',
        ];

        $statusList = ['Disetujui', 'Disetujui', 'Disetujui', 'Pending', 'Ditolak'];

        for ($i = 0; $i < 40; $i++) {
            $mulai   = Carbon::now()->subDays(rand(1, 180));
            $lama    = rand(1, 14);
            $selesai = (clone $mulai)->addDays($lama - 1);
            $status  = $statusList[$i % count($statusList)];

            CutiIzin::create([
                'nama_pegawai'    => $pegawai[$i % count($pegawai)],
                'jenis_cuti_izin' => $jenisCuti[$i % count($jenisCuti)],
                'tanggal_mulai'   => $mulai->toDateString(),
                'tanggal_selesai' => $selesai->toDateString(),
                'lama_hari'       => $lama,
                'alasan'          => $alasan[$i % count($alasan)],
                'status'          => $status,
            ]);
        }
    }
}
