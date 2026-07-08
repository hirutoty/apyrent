<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VendorPricelist;
use Carbon\Carbon;

class VendorPricelistSeeder extends Seeder
{
    public function run(): void
    {
        $vendors    = ['PT Maju Jaya', 'CV Berkah Abadi', 'PT Sumber Makmur', 'UD Sejahtera', 'PT Indo Supplier'];
        $kodeBarang = ['BRG-001', 'BRG-002', 'BRG-003', 'BRG-004', 'BRG-005', 'BRG-006', 'BRG-007', 'BRG-008'];
        $namaBarang = ['Spare Part Mesin', 'Oli Mesin 10W-40', 'Ban Kendaraan', 'Filter Udara', 'Aki Kendaraan', 'Kampas Rem', 'Radiator Coolant', 'Busi Platinum'];
        $satuan     = ['pcs', 'liter', 'unit', 'set', 'buah'];

        for ($i = 1; $i <= 25; $i++) {
            VendorPricelist::updateOrCreate(
                [
                    'vendor'      => $vendors[($i - 1) % count($vendors)],
                    'kode_barang' => $kodeBarang[($i - 1) % count($kodeBarang)],
                ],
                [
                    'vendor'          => $vendors[($i - 1) % count($vendors)],
                    'kode_barang'     => $kodeBarang[($i - 1) % count($kodeBarang)],
                    'nama_barang'     => $namaBarang[($i - 1) % count($namaBarang)],
                    'harga_per_unit'  => rand(25000, 1500000),
                    'satuan'          => $satuan[($i - 1) % count($satuan)],
                    'diskon'          => round(rand(0, 20) + (rand(0, 99) / 100), 2),
                    'minimal_order'   => rand(1, 50),
                    'lead_time'       => rand(3, 30),
                    'tanggal_berlaku' => Carbon::now()->subDays(rand(0, 90))->startOfMonth(),
                ]
            );
        }
    }
}
