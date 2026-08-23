<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataLeasing;
use App\Models\DataKontrak;
use App\Models\Kendaraan;
use Carbon\Carbon;

class DataLeasingSeeder extends Seeder
{
    public function run(): void
    {
        $kendaraanIds = Kendaraan::pluck('id')->toArray();
        $kontrakIds   = DataKontrak::pluck('id')->toArray();

        if (empty($kendaraanIds)) {
            $this->command->warn('Tidak ada data kendaraan. Jalankan KendaraanSeeder terlebih dahulu.');
            return;
        }

        $caraBayar   = ['Auto Debit', 'Transfer', 'Tunai'];
        $sumberDana  = ['BCA', 'BRI', 'BNI', 'Mandiri', 'BSI', 'Bank Jateng'];
        $asuransi    = ['Jasindo', 'Sinar Mas', 'Adira', 'ACA', 'Wahana Tata', 'Bumi Putera'];
        $userLeasing = [
            'Budi Hartono', 'Siti Rahayu', 'Ahmad Fauzi', 'Dewi Permatasari',
            'Eko Purnomo', 'Fatimah Zahra', 'Gunawan Wibowo', 'Heni Susanti',
            'Irwan Setiawan', 'Joko Widodo', 'Kurniawan', 'Linda Sari',
            'Muhammad Rizki', 'Nanda Pratiwi', 'Oci Marlina', 'Pandu Wijaya',
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

                // ~2/3 terhubung ke DataKontrak
                $dataKontrakId = null;
                $noKontrak     = null;

                if (!empty($kontrakIds) && $idx % 3 !== 0) {
                    $kontrak       = DataKontrak::find($kontrakIds[($idx - 1) % count($kontrakIds)]);
                    $dataKontrakId = $kontrak->id;
                    $noKontrak     = $kontrak->no_kontrak;
                    $angsuran      = $kontrak->angsuran_per_bulan;
                    $jatuhTempo    = $kontrak->jatuh_tempo ?? rand(1, 28);
                    $caraBayarV    = $kontrak->cara_bayar ?? $caraBayar[$idx % count($caraBayar)];
                    $sumber        = $kontrak->sumber_dana_debit ?? $sumberDana[$idx % count($sumberDana)];
                    $mobil         = $kontrak->mobil;
                    $nopol         = $kontrak->nopol;
                    $tahun         = $kontrak->tahun;
                    $periodeM      = $bulanIni->copy();
                    $periodeS      = $periodeM->copy()->addMonths(rand(12, 48));
                } else {
                    // Mandiri
                    $noKontrak  = 'LSG/' . $bulanIni->format('Y') . '/' . str_pad($idx, 4, '0', STR_PAD_LEFT);
                    $angsuran   = rand(2, 20) * 500000;
                    $jatuhTempo = rand(1, 28);
                    $caraBayarV = $caraBayar[$idx % count($caraBayar)];
                    $sumber     = $sumberDana[$idx % count($sumberDana)];
                    $mobil      = $kendaraan->merk ?? 'Kendaraan ' . $idx;
                    $nopol      = $kendaraan->nopol ?? 'AA ' . str_pad($idx * 77 % 9999, 4, '0') . ' CD';
                    $tahun      = $kendaraan->tahun_pembuatan ?? rand(2018, 2024);
                    $periodeM   = $bulanIni->copy();
                    $periodeS   = $periodeM->copy()->addMonths(rand(12, 36));
                }

                // jumlah_cicilan: kadang manual, kadang null
                $jumlahCicilan = ($idx % 4 === 0)
                    ? Carbon::parse($periodeM)->diffInMonths(Carbon::parse($periodeS))
                    : null;

                DataLeasing::create([
                    'data_kontrak_id'    => $dataKontrakId,
                    'no_kontrak'         => $noKontrak,
                    'mobil'              => $mobil,
                    'tahun'              => (string) $tahun,
                    'nopol'              => $nopol,
                    'user_leasing'       => $userLeasing[($idx - 1) % count($userLeasing)],
                    'angsuran_per_bulan' => $angsuran,
                    'jatuh_tempo'        => $jatuhTempo,
                    'periode_mulai'      => Carbon::parse($periodeM)->toDateString(),
                    'periode_selesai'    => Carbon::parse($periodeS)->toDateString(),
                    'jumlah_cicilan'     => $jumlahCicilan,
                    'personal_account'   => $sumber . ' - ' . str_pad(rand(1000000000, 9999999999), 10, '0'),
                    'sumber_dana_debit'  => $sumber,
                    'cara_bayar'         => $caraBayarV,
                    'asuransi_leasing'   => $asuransi[$idx % count($asuransi)],
                ]);

                $count++;
            }
        }

        $this->command->info("DataLeasingSeeder: {$count} data leasing berhasil dibuat ({$perBulan}/bulan × {$totalBulan} bulan).");
    }
}
