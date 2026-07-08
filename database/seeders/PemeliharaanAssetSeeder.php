<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PemeliharaanAsset;
use Carbon\Carbon;

class PemeliharaanAssetSeeder extends Seeder
{
    public function run(): void
    {
        $kodeAsset    = array_map(fn($i) => 'AST-' . str_pad($i, 3, '0', STR_PAD_LEFT), range(1, 15));
        $jenisService = ['Preventif', 'Korektif', 'Penggantian Spare Part', 'Kalibrasi', 'Pembersihan Rutin'];
        $vendor       = ['PT Servis Jaya', 'CV Teknik Maju', 'UD Bengkel Mandiri', 'PT AutoCare', 'Teknisi Internal'];
        $statusList   = ['Selesai', 'Dalam Proses', 'Dijadwalkan'];

        for ($i = 1; $i <= 25; $i++) {
            $tgl    = Carbon::now()->subDays(rand(1, 365));
            $status = $statusList[($i - 1) % count($statusList)];
            PemeliharaanAsset::create([
                'kode_aset'          => $kodeAsset[($i - 1) % count($kodeAsset)],
                'tanggal_service'    => $tgl,
                'jenis_service'      => $jenisService[($i - 1) % count($jenisService)],
                'vendor_pic'         => $vendor[($i - 1) % count($vendor)],
                'biaya'              => rand(150, 15000) * 1000,
                'status'             => $status,
                'jadwal_selanjutnya' => $status === 'Selesai' ? (clone $tgl)->addMonths(rand(3, 12)) : null,
                'catatan'            => 'Service ke-' . $i . ' untuk pengecekan berkala',
            ]);
        }
    }
}
