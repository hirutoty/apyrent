<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Purchasero;
use Carbon\Carbon;

class PurchaseroSeeder extends Seeder
{
    public function run(): void
    {
        $departemen   = ['Produksi', 'Gudang', 'IT', 'Finance', 'HR', 'Marketing', 'Operasional', 'Maintenance'];
        $barangJasa   = ['Spare Part', 'ATK', 'Komputer', 'Bahan Bakar', 'Oli Mesin', 'Ban Kendaraan', 'Seragam', 'Alat Kebersihan', 'Mebel', 'Printer'];
        $satuan       = ['pcs', 'unit', 'liter', 'kg', 'set', 'dus', 'rim', 'buah'];
        $alasan       = ['Stok Habis', 'Persediaan Menipis', 'Permintaan Proyek', 'Penggantian Rutin', 'Kebutuhan Mendadak'];
        $statusList   = ['Pending', 'Disetujui', 'Ditolak', 'Selesai'];

        for ($i = 1; $i <= 50; $i++) {
            $status           = $statusList[($i - 1) % count($statusList)];
            $tanggal          = Carbon::now()->subDays(rand(1, 180));
            $disetujui        = in_array($status, ['Disetujui', 'Selesai']);
            $tglPersetujuan   = $disetujui ? (clone $tanggal)->addDays(rand(1, 3)) : null;

            Purchasero::updateOrCreate(
                ['no_pr' => 'PR-' . str_pad($i, 3, '0', STR_PAD_LEFT)],
                [
                    'no_pr'               => 'PR-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'tanggal'             => $tanggal,
                    'departemen'          => $departemen[($i - 1) % count($departemen)],
                    'pemohon'             => 'Pemohon ' . $i,
                    'barang_jasa'         => $barangJasa[($i - 1) % count($barangJasa)],
                    'kode_barang'         => 'BRG-' . str_pad($i * 7 % 999, 3, '0', STR_PAD_LEFT),
                    'qty'                 => rand(1, 500),
                    'satuan'              => $satuan[($i - 1) % count($satuan)],
                    'alasan_permintaan'   => $alasan[($i - 1) % count($alasan)],
                    'status'              => $status,
                    'disetujui_oleh'      => $disetujui ? 'Manajer ' . $departemen[($i - 1) % count($departemen)] : null,
                    'tanggal_persetujuan' => $tglPersetujuan,
                    'catatan'             => 'Catatan PR ke-' . $i,
                ]
            );
        }
    }
}
