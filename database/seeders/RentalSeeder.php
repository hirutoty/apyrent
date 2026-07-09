<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rental;
use Carbon\Carbon;

class RentalSeeder extends Seeder
{
    public function run(): void
    {
        $statuses    = ['Pending', 'booking', 'aktif', 'selesai', 'batal'];
        $metodeBayar = ['tunai', 'transfer'];
        $jenisBayar  = ['lunas', 'dp'];
        $statusBayar = ['belum_bayar', 'dp', 'lunas'];

        for ($i = 1; $i <= 50; $i++) {
            $kendaraanId = (($i - 1) % 50) + 1;
            $memberId    = (($i - 1) % 50) + 1;

            $mulai    = Carbon::now()->subDays(rand(1, 180));
            $durHari  = rand(1, 14);
            $selesai  = (clone $mulai)->addDays($durHari);

            $biayaDasar  = $durHari * rand(200, 600) * 1000;
            $biayaTambah = rand(0, 500) * 1000;
            $total       = $biayaDasar + $biayaTambah;

            $status      = $statuses[($i - 1) % count($statuses)];
            $metodePembayaran = $metodeBayar[($i - 1) % count($metodeBayar)];
            $jenisPembayaran  = $jenisBayar[($i - 1) % count($jenisBayar)];
            $statusPembayaran = $statusBayar[($i - 1) % count($statusBayar)];

            Rental::create([
                'user_id'               => 1,
                'kendaraan_id'          => $kendaraanId,
                'member_id'             => $memberId,
                'tanggal_mulai'         => $mulai,
                'tanggal_selesai'       => $selesai,
                'tujuan'                => 'Perjalanan dinas ke kota ' . $i,
                'durasi_jam'            => rand(0, 8),
                'durasi_hari'           => $durHari,
                'durasi_bulan'          => 0,
                'biaya_dasar'           => $biayaDasar,
                'biaya_tambahan_total'  => $biayaTambah,
                'total_biaya'           => $total,
                'metode_pembayaran'     => $metodePembayaran,
                'jenis_pembayaran'      => $jenisPembayaran,
                'nominal_dp'            => $jenisPembayaran === 'dp' ? intval($total * 0.5) : null,
                'nama_driver'           => rand(0, 1) ? 'Driver ' . $i : null,
                'kontak_driver'         => rand(0, 1) ? '08' . rand(100000000, 999999999) : null,
                'biaya_driver'          => rand(0, 1) ? rand(50, 200) * 1000 : null,
                'status_pembayaran'     => $statusPembayaran,
                'status'                => $status,
            ]);
        }
    }
}

