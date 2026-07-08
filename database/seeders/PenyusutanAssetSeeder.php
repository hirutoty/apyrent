<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PenyusutanAsset;

class PenyusutanAssetSeeder extends Seeder
{
    public function run(): void
    {
        $kodeAsset = array_map(fn($i) => 'AST-' . str_pad($i, 3, '0', STR_PAD_LEFT), range(1, 15));
        $metode    = ['Garis Lurus', 'Saldo Menurun', 'Unit Produksi'];
        $tahunList = [2022, 2023, 2024, 2025, 2026];

        foreach ($kodeAsset as $idx => $kode) {
            $nilaiAwal = rand(10, 500) * 1000000;
            $penyusutan = (int)($nilaiAwal * 0.2); // 20% per tahun
            foreach ($tahunList as $tIdx => $tahun) {
                $akumulasi = $penyusutan * ($tIdx + 1);
                $nilaiBuku = max(0, $nilaiAwal - $akumulasi);
                PenyusutanAsset::create([
                    'kode_aset'            => $kode,
                    'tahun'                => $tahun,
                    'nilai_awal'           => $nilaiAwal,
                    'akumulasi_penyusutan' => $akumulasi,
                    'nilai_buku'           => $nilaiBuku,
                    'metode'               => $metode[$idx % count($metode)],
                    'status'               => $tahun < 2026 ? 'Selesai' : 'Aktif',
                ]);
            }
        }
    }
}
