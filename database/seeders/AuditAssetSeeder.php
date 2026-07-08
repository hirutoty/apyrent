<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AuditAsset;
use Carbon\Carbon;

class AuditAssetSeeder extends Seeder
{
    public function run(): void
    {
        $kodeAsset   = array_map(fn($i) => 'AST-' . str_pad($i, 3, '0', STR_PAD_LEFT), range(1, 15));
        $pemeriksa   = ['Tim Audit Internal', 'Auditor Eksternal', 'Kepala Gudang', 'Manajer IT', 'Supervisor Operasional'];
        $statusFisik = ['Baik', 'Rusak Ringan', 'Rusak Berat', 'Hilang'];
        $temuanList  = ['Tidak Ada Temuan', 'Ada Temuan Minor', 'Ada Temuan Mayor'];
        $tindakan    = ['Diperbaiki segera', 'Dijadwalkan perbaikan bulan depan', 'Dilaporkan ke manajemen', null, null];

        for ($i = 1; $i <= 20; $i++) {
            $sf = $statusFisik[($i - 1) % count($statusFisik)];
            $tm = $temuanList[($i - 1) % count($temuanList)];
            AuditAsset::create([
                'kode_aset'      => $kodeAsset[($i - 1) % count($kodeAsset)],
                'tanggal_audit'  => Carbon::now()->subDays(rand(1, 365)),
                'diperiksa_oleh' => $pemeriksa[($i - 1) % count($pemeriksa)],
                'status_fisik'   => $sf,
                'temuan'         => $tm,
                'tindakan'       => $tm !== 'Tidak Ada Temuan' ? $tindakan[$i % count($tindakan)] : null,
                'catatan'        => 'Audit rutin periode ' . date('Y') . ' — aset #' . $i,
            ]);
        }
    }
}
