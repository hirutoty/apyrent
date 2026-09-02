<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PajakKendaraan;
use Carbon\Carbon;

class PajakKendaraanSeeder extends Seeder
{
    public function run(): void
    {
        $jenisPajak = ['Pajak Tahunan', 'Pajak 5 Tahunan', 'STNK', 'BPKB', 'BBN-KB'];
        $keterangan = [
            'Pajak hampir jatuh tempo',
            'Segera lakukan pembayaran',
            'Sudah melewati jatuh tempo',
            'Pembayaran berhasil',
            'Perlu segera diperpanjang',
            'Menunggu verifikasi',
            'Dalam proses pembayaran',
        ];

        $year = 2026;

        for ($i = 1; $i <= 50; $i++) {
            $sudahBayar = ($i % 3 === 0);
            
            // Jatuh tempo: random di 2026-2027
            $jatuhTempo = Carbon::create($year, rand(1, 12), rand(1, 28));
            
            // Tanggal bayar: tersebar merata di Jan-Des 2026 untuk yang sudah bayar
            if ($sudahBayar) {
                $month = (($i - 1) % 12) + 1;
                $day = rand(1, Carbon::create($year, $month, 1)->daysInMonth);
                $tanggalBayar = Carbon::create($year, $month, $day)
                    ->setTime(rand(8, 17), rand(0, 59), rand(0, 59));
            } else {
                $tanggalBayar = null;
            }

            PajakKendaraan::create([
                'kendaraan_id'   => (($i - 1) % 50) + 1,
                'jenis_pajak'    => $jenisPajak[($i - 1) % count($jenisPajak)],
                'nominal'        => rand(5, 60) * 100000,
                'jatuh_tempo'    => $jatuhTempo,
                'tanggal_bayar'  => $tanggalBayar,
                'status'         => $sudahBayar ? 'sudah_bayar' : 'belum_bayar',
                'keterangan'     => $keterangan[($i - 1) % count($keterangan)],
                'bukti'          => null,
                'created_at'     => $tanggalBayar ?? Carbon::create($year, rand(1, 9), rand(1, 28)),
                'updated_at'     => $tanggalBayar ?? Carbon::create($year, rand(1, 9), rand(1, 28)),
            ]);
        }
    }
}
