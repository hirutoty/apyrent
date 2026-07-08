<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dropshipping;
use Carbon\Carbon;

class DropshippingSeeder extends Seeder
{
    public function run(): void
    {
        $vendors   = ['PT Maju Jaya', 'CV Berkah Abadi', 'PT Sumber Makmur', 'UD Sejahtera', 'PT Indo Supplier'];
        $barang    = ['Spare Part Mesin', 'Oli Mesin', 'Ban Kendaraan', 'Filter Udara', 'Aki Kendaraan', 'Kampas Rem'];
        $satuan    = ['pcs', 'liter', 'unit', 'set', 'buah'];
        $tipe      = ['Regular', 'Express', 'Same Day', 'Ekonomi'];
        $customers = ['PT Angin Ribut', 'CV Cahaya Terang', 'Toko Maju', 'UD Bahagia', 'PT Kilat Jaya', 'CV Sentosa'];
        $statusList = ['Proses', 'Dikirim', 'Selesai'];

        for ($i = 1; $i <= 25; $i++) {
            $tglKirim = Carbon::now()->subDays(rand(1, 120));

            Dropshipping::updateOrCreate(
                ['kode_transaksi' => 'DS-' . str_pad($i, 3, '0', STR_PAD_LEFT)],
                [
                    'kode_transaksi' => 'DS-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'tipe'           => $tipe[($i - 1) % count($tipe)],
                    'vendor'         => $vendors[($i - 1) % count($vendors)],
                    'barang'         => $barang[($i - 1) % count($barang)],
                    'jumlah'         => rand(1, 100),
                    'satuan'         => $satuan[($i - 1) % count($satuan)],
                    'customer_akhir' => $customers[($i - 1) % count($customers)],
                    'tanggal_kirim'  => $tglKirim,
                    'status'         => $statusList[($i - 1) % count($statusList)],
                ]
            );
        }
    }
}
