<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔧 Starting update keterangan_limit...\n\n";

// 1. Update service_parts
echo "1️⃣  Updating service_parts table...\n";
$parts = DB::table('service_parts')
    ->whereNotNull('keterangan_limit')
    ->where('keterangan_limit', '!=', '-')
    ->get();

$countParts = 0;
foreach ($parts as $part) {
    $ket = $part->keterangan_limit;
    
    $hasJangkaWaktu = (
        stripos($ket, 'limit jangka waktu') !== false
    );
    
    $hasOtherLimit = (
        stripos($ket, 'limit KM') !== false ||
        stripos($ket, 'limit biaya') !== false ||
        stripos($ket, 'pemasangan part') !== false
    );
    
    if ($hasJangkaWaktu && !$hasOtherLimit) {
        DB::table('service_parts')
            ->where('id', $part->id)
            ->update(['keterangan_limit' => '-']);
        $countParts++;
        echo "  ✓ Updated service_part #{$part->id}\n";
    }
}
echo "  ✅ Total: {$countParts} records updated\n\n";

// 2. Update purchase_orders
echo "2️⃣  Updating purchase_orders table...\n";
$pos = DB::table('purchase_orders')
    ->whereIn('source_type', ['service_part', 'service_incident'])
    ->whereNotNull('source_data')
    ->get();

$countPO = 0;
foreach ($pos as $po) {
    $sourceData = json_decode($po->source_data, true);
    if (!isset($sourceData['parts']) || !is_array($sourceData['parts'])) {
        continue;
    }

    $modified = false;
    foreach ($sourceData['parts'] as &$part) {
        $ket = $part['keterangan_limit'] ?? '';
        
        $hasJangkaWaktu = (
            stripos($ket, 'limit jangka waktu') !== false
        );
        
        $hasOtherLimit = (
            stripos($ket, 'limit KM') !== false ||
            stripos($ket, 'limit biaya') !== false ||
            stripos($ket, 'pemasangan part') !== false
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
        $countPO++;
        echo "  ✓ Updated PO #{$po->id}\n";
    }
}
echo "  ✅ Total: {$countPO} records updated\n\n";

echo "🎉 Done! Updated {$countParts} service_parts and {$countPO} purchase_orders\n";
