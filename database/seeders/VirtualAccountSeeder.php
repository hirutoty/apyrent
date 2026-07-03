<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VirtualAccount;
use App\Models\Member;
use App\Models\Rental;

class VirtualAccountSeeder extends Seeder
{
    public function run(): void
    {
        $member = Member::first();
        $rental = Rental::first();

        if (!$member || !$rental) {
            return; // biar tidak error FK
        }

        VirtualAccount::create([
            'va_number' => 'VA-' . rand(10000000, 99999999),
            'member_id' => $member->id,
            'invoice_id' => null, // kalau belum ada invoice
            'bukti_pembayaran' => null,
            'bank' => 'bca',
            'expected_amount' => 1500000,
            'paid_amount' => 0,
            'status' => 'Pending',
        ]);

        VirtualAccount::create([
            'va_number' => 'VA-' . rand(10000000, 99999999),
            'member_id' => $member->id,
            'invoice_id' => null,
            'bukti_pembayaran' => null,
            'bank' => 'bni',
            'expected_amount' => 2500000,
            'paid_amount' => 2500000,
            'status' => 'paid',
        ]);
    }
}