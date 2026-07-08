<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RequestforQuotation;
use Carbon\Carbon;

class RequestforQuotationSeeder extends Seeder
{
    public function run(): void
    {
        $vendors     = ['PT Maju Jaya', 'CV Berkah Abadi', 'PT Sumber Makmur', 'UD Sejahtera', 'PT Indo Supplier'];
        $kodeBarang  = ['BRG-001', 'BRG-002', 'BRG-003', 'BRG-004', 'BRG-005', 'BRG-006', 'BRG-007', 'BRG-008'];
        $namaBarang  = ['Spare Part Mesin', 'Oli Mesin 10W-40', 'Ban Kendaraan', 'Filter Udara', 'Aki Kendaraan', 'Kampas Rem', 'Radiator Coolant', 'Busi Platinum'];
        $satuan      = ['pcs', 'liter', 'unit', 'set', 'buah', 'dus', 'kg'];
        $statusList  = ['Open', 'Sent', 'Closed'];

        for ($i = 1; $i <= 30; $i++) {
            $tglRfq   = Carbon::now()->subDays(rand(1, 180));
            $tglKirim = (clone $tglRfq)->addDays(rand(7, 30));
            $idx      = ($i - 1) % count($vendors);

            RequestforQuotation::updateOrCreate(
                ['id_rfq' => 'RFQ-' . str_pad($i, 3, '0', STR_PAD_LEFT)],
                [
                    'id_rfq'         => 'RFQ-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'tanggal_rfq'    => $tglRfq,
                    'vendor'         => $vendors[$idx],
                    'kode_barang'    => $kodeBarang[($i - 1) % count($kodeBarang)],
                    'nama_barang'    => $namaBarang[($i - 1) % count($namaBarang)],
                    'kuantitas'      => rand(10, 500),
                    'satuan'         => $satuan[($i - 1) % count($satuan)],
                    'harga_estimasi' => rand(50000, 2000000),
                    'tanggal_kirim'  => $tglKirim,
                    'status_rfq'     => $statusList[($i - 1) % count($statusList)],
                    'catatan'        => 'Catatan RFQ ke-' . $i,
                ]
            );
        }
    }
}
