<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invoice;
use App\Models\InvKontrak;
use App\Models\InvoicePeriode;
use App\Models\InvoiceRemak;
use Carbon\Carbon;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $kontraks = InvKontrak::with('penawaran')->get();

        $statusList        = ['draft', 'partial', 'overdue', 'lunas'];
        $paymentStatusList = ['unpaid', 'paid'];
        $satuanList        = ['Bulan', 'Hari', 'Tahun'];
        $remakList         = [
            'Sewa Kendaraan Operasional',
            'Biaya Driver',
            'Bahan Bakar',
            'Biaya Perawatan',
            'Asuransi Kendaraan',
            'Biaya Administrasi',
        ];

        foreach ($kontraks as $idx => $kontrak) {
            $p       = $kontrak->penawaran;
            $invDate = Carbon::parse($kontrak->tanggal_kontrak)->addDays(rand(1, 7));
            $total   = $p ? (float) $p->total : rand(5000000, 50000000);
            $ppn     = round($total * 0.11, 2);
            $pph     = round($total * 0.02, 2);
            $status  = $statusList[$idx % count($statusList)];
            $payStatus = in_array($status, ['lunas']) ? 'paid' : 'unpaid';

            $invoice = Invoice::create([
                'penawaran_id'    => $p ? $p->id : null,
                'kontrak_id'      => $kontrak->id,
                'kendaraan_id'    => null,
                'type'            => ($idx % 3 === 0) ? 'perorangan' : 'perusahaan',
                'invoice_no'      => 'INV-' . date('Y') . '-' . str_pad($idx + 1, 4, '0', STR_PAD_LEFT),
                'order_no'        => 'ORD-' . str_pad($idx + 1, 4, '0', STR_PAD_LEFT),
                'customer_name'   => $kontrak->pihak_kedua,
                'customer_address'=> 'Jl. Contoh No.' . ($idx + 1) . ', Jakarta',
                'contact_person'  => $kontrak->contact_kedua,
                'telephone'       => '0812' . rand(10000000, 99999999),
                'email'           => strtolower(str_replace(' ', '.', $kontrak->pihak_kedua ?? 'customer')) . '@email.com',
                'satuan'          => $satuanList[$idx % count($satuanList)],
                'invoice_date'    => $invDate->toDateString(),
                'pengirim'        => 'Divisi Finance',
                'staff'           => 'Staff Finance',
                'name_staff'      => 'Wahyu Nugroho',
                'direktur'        => 'Direktur',
                'name_direktur'   => 'Budi Santoso',
                'status'          => $status,
                'payment_status'  => $payStatus,
                'ppn'             => $ppn,
                'pph'             => $pph,
                'total'           => $total + $ppn - $pph,
            ]);

            // Periode sewa
            $periodeAwal  = $invDate->copy();
            $periodeAkhir = $periodeAwal->copy()->addMonths(rand(1, 6));
            $periode = InvoicePeriode::create([
                'invoice_id'   => $invoice->id,
                'periode_awal' => $periodeAwal->toDateString(),
                'periode_akhir'=> $periodeAkhir->toDateString(),
            ]);

            // Rincian item (remaks)
            $jumlahRemak = rand(1, 3);
            for ($r = 0; $r < $jumlahRemak; $r++) {
                $qty   = rand(1, 4);
                $price = rand(500000, 5000000);
                InvoiceRemak::create([
                    'invoice_id' => $invoice->id,
                    'periode_id' => $periode->id,
                    'remaks'     => $remakList[($idx + $r) % count($remakList)],
                    'qty'        => $qty,
                    'price'      => $price,
                ]);
            }
        }
    }
}
