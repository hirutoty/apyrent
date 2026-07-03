<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Purchasero;

class PurchaseroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Purchasero::create([
            'no_pr' => 'PR-001',
            'tanggal' => '2025-06-25',
            'departemen' => 'Produksi',
            'pemohon' => 'Rina',
            'barang_jasa' => 'Label Baju',
            'kode_barang' => 'BRG-003',
            'qty' => 500,
            'satuan' => 'pcs',
            'alasan_permintaan' => 'Stok Habis',
            'status' => 'Disetujui',
            'disetujui_oleh' => 'Manajer Produksi',
            'tanggal_persetujuan' => '2025-06-26',
            'catatan' => 'Segera diproses.',
        ]);

        Purchasero::create([
            'no_pr' => 'PR-002',
            'tanggal' => '2025-06-27',
            'departemen' => 'Gudang',
            'pemohon' => 'Andi',
            'barang_jasa' => 'Kardus Packing',
            'kode_barang' => 'BRG-010',
            'qty' => 200,
            'satuan' => 'pcs',
            'alasan_permintaan' => 'Persediaan Menipis',
            'status' => 'Pending',
            'disetujui_oleh' => null,
            'tanggal_persetujuan' => null,
            'catatan' => 'Menunggu persetujuan manager.',
        ]);
    }
}