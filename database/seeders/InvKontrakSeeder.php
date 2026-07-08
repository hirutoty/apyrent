<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InvKontrak;
use App\Models\InvPenawaran;
use Carbon\Carbon;

class InvKontrakSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil penawaran yang statusnya approved/active/completed
        $penawarans = InvPenawaran::whereIn('status', ['approved', 'active', 'completed'])->get();

        $statusList = ['pending', 'approved', 'active', 'completed', 'terminated'];

        foreach ($penawarans as $idx => $p) {
            $tglKontrak = Carbon::parse($p->tanggal_penawaran)->addDays(rand(3, 14));
            $jatuhTempo = (clone $tglKontrak)->addMonths(rand(1, 12));

            InvKontrak::create([
                'penawaran_id'          => $p->id,
                'no_kontrak'            => 'KTR-' . str_pad($p->id, 4, '0', STR_PAD_LEFT),
                'tanggal_kontrak'       => $tglKontrak->toDateString(),
                'perjanjian_pembayaran' => $jatuhTempo->toDateString(),
                'pihak_pertama'         => 'PT Apyrent Indonesia',
                'contact_pertama'       => '021-12345678',
                'pihak_kedua'           => $p->customer_name,
                'contact_kedua'         => $p->contact_person,
                'file_kontrak'          => null,
                'file_persyaratan'      => null,
                'status'                => $statusList[$idx % count($statusList)],
            ]);
        }
    }
}
