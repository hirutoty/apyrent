<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataKontrak;
use App\Models\Kendaraan;
use Carbon\Carbon;

class DataKontrakSeeder extends Seeder
{
    public function run(): void
    {
        $kendaraanIds = Kendaraan::pluck('id')->toArray();

        if (empty($kendaraanIds)) {
            $this->command->warn('Tidak ada data kendaraan. Jalankan KendaraanSeeder terlebih dahulu.');
            return;
        }

        $caraBayar     = ['Auto Debit', 'Transfer', 'Tunai'];
        $sumberDana    = ['BCA', 'BRI', 'BNI', 'Mandiri', 'BSI', 'Bank Jateng'];
        $namaAsuransi  = ['Asuransi Jasindo', 'Asuransi Sinar Mas', 'Asuransi Adira', 'Asuransi ACA', 'Asuransi Wahana Tata'];
        $namaBengkel   = ['Bengkel Resmi Toyota', 'Bengkel Resmi Honda', 'Bengkel Mitra Utama', 'Auto 2000', 'Nasmoco'];
        $namaMarketing = ['Budi Santoso', 'Rina Wijaya', 'Agus Prasetyo', 'Dewi Lestari', 'Hendra Kusuma'];
        $userKontrak   = [
            'PT. Maju Jaya Abadi', 'CV. Sumber Rezeki', 'PT. Karya Nusantara',
            'CV. Berkah Mandiri', 'PT. Cipta Sukses', 'Yayasan Tunas Bangsa',
            'PT. Armada Sentosa', 'CV. Usaha Bersama', 'PT. Garuda Prima',
            'CV. Wahyu Abadi', 'PT. Selaras Jaya', 'CV. Mitra Sejahtera',
        ];

        // 10 data per bulan × 12 bulan = 120 data
        $perBulan   = 10;
        $totalBulan = 12;
        $idx        = 1;
        $count      = 0;

        for ($bulan = $totalBulan - 1; $bulan >= 0; $bulan--) {
            $bulanIni = Carbon::now()->subMonths($bulan)->startOfMonth();

            for ($j = 0; $j < $perBulan; $j++, $idx++) {
                $kendaraanId = $kendaraanIds[($idx - 1) % count($kendaraanIds)];
                $kendaraan   = Kendaraan::find($kendaraanId);

                $durasi   = rand(12, 48);
                $periodeM = $bulanIni->copy();
                $periodeS = $periodeM->copy()->addMonths($durasi);

                $angsuran   = rand(2, 20) * 500000;
                $jatuhTempo = rand(1, 28);
                $caraBayarV = $caraBayar[$idx % count($caraBayar)];
                $sumber     = $sumberDana[$idx % count($sumberDana)];

                $serial    = 'KTR-' . $periodeM->format('Ym') . '-' . str_pad($idx, 4, '0', STR_PAD_LEFT);
                $noKontrak = 'KTR/' . $periodeM->format('Y') . '/' . str_pad($idx, 4, '0', STR_PAD_LEFT);

                DataKontrak::create([
                    'serial_number'      => $serial,
                    'no_kontrak'         => $noKontrak,
                    'kendaraan_id'       => $kendaraanId,
                    'mobil'              => $kendaraan->merk ?? 'Kendaraan ' . $idx,
                    'nopol'              => $kendaraan->nopol ?? 'AA ' . str_pad($idx * 123 % 9999, 4, '0') . ' AB',
                    'tahun'              => (string) ($kendaraan->tahun_pembuatan ?? rand(2018, 2024)),
                    'user_kontrak'       => $userKontrak[($idx - 1) % count($userKontrak)],
                    'angsuran_per_bulan' => $angsuran,
                    'jatuh_tempo'        => $jatuhTempo,
                    'periode_mulai'      => $periodeM->toDateString(),
                    'periode_selesai'    => $periodeS->toDateString(),
                    'personal_account'   => $sumber . ' - ' . str_pad(rand(1000000000, 9999999999), 10, '0'),
                    'sumber_dana_debit'  => $sumber,
                    'cara_bayar'         => $caraBayarV,
                    'nama_asuransi'      => $namaAsuransi[$idx % count($namaAsuransi)],
                    'alamat_asuransi'    => 'Jl. Jenderal Sudirman No. ' . ($idx * 3) . ', Jakarta',
                    'nama_marketing'     => $namaMarketing[$idx % count($namaMarketing)],
                    'kontak_marketing'   => '0812' . str_pad(rand(10000000, 99999999), 8, '0'),
                    'nama_bengkel'       => $namaBengkel[$idx % count($namaBengkel)],
                    'kontak_bengkel'     => '0274-' . rand(100000, 999999),
                    'bukti'              => null,
                ]);

                $count++;
            }
        }

        $this->command->info("DataKontrakSeeder: {$count} data kontrak berhasil dibuat ({$perBulan}/bulan × {$totalBulan} bulan).");
    }
}
