<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VirtualAccount;
use App\Models\Pelanggan;
use App\Models\Invoice;
use Carbon\Carbon;

class VirtualAccountSeeder extends Seeder
{
    public function run(): void
    {
        $pelangganIds = Pelanggan::pluck('id')->toArray();
        $invoiceIds   = Invoice::pluck('id')->toArray();

        if (empty($pelangganIds)) {
            $this->command->error('Tidak ada data pelanggan. Jalankan PelangganSeeder terlebih dahulu.');
            return;
        }

        $banks = ['bca', 'bni', 'mandiri', 'bri', 'permata'];
        $statusPool = ['pending', 'pending', 'paid', 'paid', 'paid', 'expired', 'canceled'];
        
        $year = 2026;

        // Generate 100 Virtual Account tersebar Jan-Des 2026
        // ~8-9 per bulan
        for ($month = 1; $month <= 12; $month++) {
            $jumlahPerBulan = ($month <= 11) ? 8 : 12; // Total 100 (88 + 12)
            $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

            for ($j = 0; $j < $jumlahPerBulan; $j++) {
                $day = rand(1, $daysInMonth);
                $createdAt = Carbon::create($year, $month, $day)
                    ->setTime(rand(8, 17), rand(0, 59), rand(0, 59));

                $status        = $statusPool[array_rand($statusPool)];
                $expectedAmount = rand(500, 10000) * 1000; // 500rb - 10jt
                
                // Paid amount sesuai status
                if ($status === 'paid') {
                    $paidAmount = $expectedAmount;
                } elseif ($status === 'pending') {
                    $paidAmount = rand(0, 1) ? intval($expectedAmount * 0.3) : 0; // DP atau 0
                } else {
                    $paidAmount = 0;
                }

                $expiredAt = $createdAt->copy()->addDays(rand(1, 7));

                VirtualAccount::create([
                    'va_number'         => 'VA-' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . rand(10000, 99999),
                    'member_id'         => $pelangganIds[array_rand($pelangganIds)],
                    'invoice_id'        => !empty($invoiceIds) && rand(0, 1) ? $invoiceIds[array_rand($invoiceIds)] : null,
                    'bukti_pembayaran'  => $status === 'paid' ? 'bukti_' . time() . '.jpg' : null,
                    'bank'              => $banks[array_rand($banks)],
                    'expected_amount'   => $expectedAmount,
                    'paid_amount'       => $paidAmount,
                    'status'            => $status,
                    'expired_at'        => $expiredAt,
                    'created_at'        => $createdAt,
                    'updated_at'        => $createdAt,
                ]);
            }
        }

        $this->command->info('VirtualAccountSeeder selesai: 100 VA dibuat (8-9 per bulan Jan-Des 2026).');
    }
}
