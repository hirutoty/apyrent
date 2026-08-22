<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rental;
use App\Models\Kendaraan;
use App\Models\Pelanggan;
use Carbon\Carbon;

class RentalSeeder extends Seeder
{
    public function run(): void
    {
        $year      = 2026;
        $startDate = Carbon::create($year, 1, 1);
        $endDate   = Carbon::create($year, 12, 30);

        $kendaraanIds  = Kendaraan::pluck('id')->toArray();
        $pelangganIds  = Pelanggan::pluck('id')->toArray();
        $userId        = \App\Models\User::first()?->id ?? 1;

        if (empty($kendaraanIds) || empty($pelangganIds)) {
            $this->command->error('Kendaraan atau Pelanggan belum ada. Jalankan seeder kendaraan/pelanggan dulu.');
            return;
        }

        $statusPool = ['Pending', 'booking', 'aktif', 'selesai', 'batal'];
        $statusWeight = [
            'Pending'  => 10,
            'booking'  => 15,
            'aktif'    => 10,
            'selesai'  => 55,
            'batal'    => 10,
        ];

        $statusPembayaranMap = [
            'Pending'  => 'belum_bayar',
            'booking'  => 'dp',
            'aktif'    => 'dp',
            'selesai'  => 'lunas',
            'batal'    => 'belum_bayar',
        ];

        $tujuanList = [
            'Perjalanan Dinas', 'Wisata Keluarga', 'Pernikahan', 'Acara Perusahaan',
            'Antar Jemput Bandara', 'Tour Kota', 'Kunjungan Bisnis', 'Mudik Lebaran',
        ];

        $driverList = [
            'Budi Santoso', 'Agus Wijaya', 'Eko Prasetyo', 'Hendra Kurniawan',
            'Rudi Hermawan', null, null, null,
        ];

        $metodePembayaran = ['tunai', 'transfer'];

        $count   = 0;
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            // 3–7 rental per minggu
            if ($current->dayOfWeek === Carbon::MONDAY) {
                $jumlah = rand(3, 7);

                for ($i = 0; $i < $jumlah; $i++) {
                    $hariOffset   = rand(0, 6);
                    $tglMulai     = $current->copy()->addDays($hariOffset);
                    if ($tglMulai->gt($endDate)) break;

                    $durasiHari   = rand(1, 7);
                    $tglSelesai   = $tglMulai->copy()->addDays($durasiHari);

                    $biayaPerHari = rand(200000, 800000);
                    $biayaDasar   = $biayaPerHari * $durasiHari;
                    $biayaTambahan = rand(0, 1) ? rand(50000, 300000) : 0;
                    $totalBiaya   = $biayaDasar + $biayaTambahan;

                    $status        = $this->weightedRandom($statusWeight);
                    $statusPembayaran = $statusPembayaranMap[$status];

                    $driver        = $driverList[array_rand($driverList)];
                    $biayaDriver   = $driver ? rand(100000, 300000) * $durasiHari : 0;
                    $totalBiaya   += $biayaDriver;

                    $nominalDp     = in_array($status, ['booking', 'aktif']) ? intval($totalBiaya * 0.3) : 0;

                    Rental::create([
                        'user_id'              => $userId,
                        'kendaraan_id'         => $kendaraanIds[array_rand($kendaraanIds)],
                        'member_id'            => $pelangganIds[array_rand($pelangganIds)],
                        'tanggal_mulai'        => $tglMulai->toDateString(),
                        'tanggal_selesai'      => $tglSelesai->toDateString(),
                        'tujuan'               => $tujuanList[array_rand($tujuanList)],
                        'tujuan_perjalanan'    => null,
                        'alamat_pengantaran'   => null,
                        'alamat_penjemputan'   => null,
                        'durasi_hari'          => $durasiHari,
                        'durasi_jam'           => 0,
                        'durasi_bulan'         => 0,
                        'durasi_tahun'         => 0,
                        'biaya_dasar'          => $biayaDasar,
                        'biaya_tambahan_total' => $biayaTambahan,
                        'total_biaya'          => $totalBiaya,
                        'metode_pembayaran'    => $metodePembayaran[array_rand($metodePembayaran)],
                        'jenis_pembayaran'     => in_array($status, ['booking', 'aktif']) ? 'dp' : 'lunas',
                        'nominal_dp'           => $nominalDp,
                        'nama_driver'          => $driver,
                        'kontak_driver'        => $driver ? '08' . rand(100000000, 999999999) : null,
                        'biaya_driver'         => $biayaDriver,
                        'status_pembayaran'    => $statusPembayaran,
                        'status'               => $status,
                        'kelayakan'            => 'layak',
                    ]);

                    $count++;
                }
            }

            $current->addDay();
        }

        $this->command->info("Seeder selesai: {$count} rental dibuat dari 1 Jan – 30 Des {$year}.");
    }

    private function weightedRandom(array $weights): string
    {
        $total      = array_sum($weights);
        $rand       = rand(1, $total);
        $cumulative = 0;

        foreach ($weights as $item => $weight) {
            $cumulative += $weight;
            if ($rand <= $cumulative) return $item;
        }

        return array_key_first($weights);
    }
}
