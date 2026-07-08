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

        for ($i = 1; $i <= 50; $i++) {
            $sudahBayar    = ($i % 3 === 0);
            $jatuhTempo    = Carbon::now()->addDays(rand(-30, 365));
            $tanggalBayar  = $sudahBayar ? Carbon::now()->subDays(rand(1, 30)) : null;

            PajakKendaraan::create([
                'kendaraan_id' => (($i - 1) % 50) + 1,
                'jenis_pajak'  => $jenisPajak[($i - 1) % count($jenisPajak)],
                'nominal'      => rand(5, 60) * 100000,
                'jatuh_tempo'  => $jatuhTempo,
                'tanggal_bayar' => $tanggalBayar,
                'status'       => $sudahBayar ? 'sudah_bayar' : 'belum_bayar',
                'keterangan'   => $keterangan[($i - 1) % count($keterangan)],
                'bukti'        => null,
            ]);
        }
    }
}
