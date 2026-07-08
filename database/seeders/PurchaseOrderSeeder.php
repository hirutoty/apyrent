<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PurchaseOrder;
use Carbon\Carbon;

class PurchaseOrderSeeder extends Seeder
{
    public function run(): void
    {
        $vendors    = ['PT Maju Jaya', 'CV Berkah Abadi', 'PT Sumber Makmur', 'UD Sejahtera', 'PT Indo Supplier'];
        $statusList = ['Pending', 'Approved', 'Closed'];

        for ($i = 1; $i <= 30; $i++) {
            $tglPo    = Carbon::now()->subDays(rand(1, 150));
            $tglKirim = (clone $tglPo)->addDays(rand(7, 21));
            $tglTerima = $statusList[($i - 1) % count($statusList)] === 'Closed'
                ? (clone $tglKirim)->addDays(rand(1, 7))
                : null;

            PurchaseOrder::updateOrCreate(
                ['po_id' => 'PO-' . str_pad($i, 3, '0', STR_PAD_LEFT)],
                [
                    'po_id'          => 'PO-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'tanggal_po'     => $tglPo,
                    'vendor'         => $vendors[($i - 1) % count($vendors)],
                    'terkait_rfq'    => 'RFQ-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'total_barang'   => rand(1, 50),
                    'total_harga'    => rand(500000, 50000000),
                    'status_po'      => $statusList[($i - 1) % count($statusList)],
                    'tanggal_kirim'  => $tglKirim,
                    'tanggal_terima' => $tglTerima,
                    'catatan'        => 'Catatan PO ke-' . $i,
                ]
            );
        }
    }
}
