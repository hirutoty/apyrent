<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateKeteranganLimitCommand extends Command
{
    protected $signature = 'service:update-keterangan-limit';
    protected $description = 'Update keterangan_limit yang hanya berisi info jangka waktu menjadi "-"';

    public function handle()
    {
        $this->info('Memulai update keterangan_limit...');

        // 1. Update service_parts table
        $updatedServiceParts = DB::table('service_parts')
            ->where(function($q) {
                $q->where('keterangan_limit', 'like', '%Belum mencapai limit jangka waktu%')
                  ->orWhere('keterangan_limit', 'like', '%Sudah mencapai batas limit jangka waktu%')
                  ->orWhere('keterangan_limit', 'like', '%Sudah melebihi batas limit jangka waktu%');
            })
            ->where(function($q) {
                $q->where('keterangan_limit', 'not like', '%limit KM%')
                  ->where('keterangan_limit', 'not like', '%limit biaya%')
                  ->where('keterangan_limit', 'not like', '%pemasangan part%');
            })
            ->update(['keterangan_limit' => '-']);

        $this->info("✅ Updated {$updatedServiceParts} records in service_parts table");

        // 2. Update purchase_orders.source_data JSON
        $purchaseOrders = DB::table('purchase_orders')
            ->whereIn('source_type', ['service_part', 'service_incident'])
            ->whereNotNull('source_data')
            ->get();

        $updatedPOCount = 0;
        foreach ($purchaseOrders as $po) {
            $sourceData = json_decode($po->source_data, true);
            if (!isset($sourceData['parts']) || !is_array($sourceData['parts'])) {
                continue;
            }

            $modified = false;
            foreach ($sourceData['parts'] as &$part) {
                $ket = $part['keterangan_limit'] ?? '';
                
                // Cek apakah hanya ada info jangka waktu tanpa limit lain
                $hasJangkaWaktu = (
                    strpos($ket, 'Belum mencapai limit jangka waktu') !== false ||
                    strpos($ket, 'Sudah mencapai batas limit jangka waktu') !== false ||
                    strpos($ket, 'Sudah melebihi batas limit jangka waktu') !== false
                );
                
                $hasOtherLimit = (
                    strpos($ket, 'limit KM') !== false ||
                    strpos($ket, 'limit biaya') !== false ||
                    strpos($ket, 'pemasangan part') !== false
                );
                
                if ($hasJangkaWaktu && !$hasOtherLimit) {
                    $part['keterangan_limit'] = '-';
                    $modified = true;
                }
            }

            if ($modified) {
                DB::table('purchase_orders')
                    ->where('id', $po->id)
                    ->update(['source_data' => json_encode($sourceData)]);
                $updatedPOCount++;
            }
        }

        $this->info("✅ Updated {$updatedPOCount} records in purchase_orders table");

        // 3. Update payment_requests.source_data JSON (jika tabel bernama payment_requests)
        $tableExists = DB::getSchemaBuilder()->hasTable('payment_requests');
        $updatedPRCount = 0;
        
        if ($tableExists) {
            $paymentRequests = DB::table('payment_requests')
                ->whereIn('source_type', ['service_part', 'service_incident'])
                ->whereNotNull('source_data')
                ->get();

            foreach ($paymentRequests as $pr) {
                $sourceData = json_decode($pr->source_data, true);
                if (!isset($sourceData['parts']) || !is_array($sourceData['parts'])) {
                    continue;
                }

                $modified = false;
                foreach ($sourceData['parts'] as &$part) {
                    $ket = $part['keterangan_limit'] ?? '';
                    
                    $hasJangkaWaktu = (
                        strpos($ket, 'Belum mencapai limit jangka waktu') !== false ||
                        strpos($ket, 'Sudah mencapai batas limit jangka waktu') !== false ||
                        strpos($ket, 'Sudah melebihi batas limit jangka waktu') !== false
                    );
                    
                    $hasOtherLimit = (
                        strpos($ket, 'limit KM') !== false ||
                        strpos($ket, 'limit biaya') !== false ||
                        strpos($ket, 'pemasangan part') !== false
                    );
                    
                    if ($hasJangkaWaktu && !$hasOtherLimit) {
                        $part['keterangan_limit'] = '-';
                        $modified = true;
                    }
                }

                if ($modified) {
                    DB::table('payment_requests')
                        ->where('id', $pr->id)
                        ->update(['source_data' => json_encode($sourceData)]);
                    $updatedPRCount++;
                }
            }
            $this->info("✅ Updated {$updatedPRCount} records in payment_requests table");
        }

        $this->info('');
        $this->info('🎉 Selesai! Total updated:');
        $this->info("   - Service Parts: {$updatedServiceParts}");
        $this->info("   - Purchase Orders: {$updatedPOCount}");
        if ($tableExists) {
            $this->info("   - Payment Requests: {$updatedPRCount}");
        }

        return 0;
    }
}
