<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PurchaseOrder;

echo "=== Check PO Status Ditolak ===\n";
$ditolak = PurchaseOrder::where('status', 'Ditolak')->count();
echo "PO dengan status 'Ditolak': " . $ditolak . "\n";

echo "\n=== Check PO Partial Rejection ===\n";
$partial = PurchaseOrder::where('status', 'Disetujui')
    ->whereRaw("JSON_SEARCH(JSON_EXTRACT(source_data, '$.item_decisions[*].action'), 'one', 'rejected') IS NOT NULL")
    ->count();
echo "PO Disetujui dengan rejected items: " . $partial . "\n";

echo "\n=== Sample PO Disetujui (first 5) ===\n";
$samples = PurchaseOrder::where('status', 'Disetujui')
    ->select('id', 'status', 'total_harga', 'source_data')
    ->take(5)
    ->get();

foreach ($samples as $po) {
    echo "\nPO #{$po->id} - Status: {$po->status} - Total: Rp " . number_format($po->total_harga, 0, ',', '.') . "\n";
    if ($po->source_data) {
        $data = is_array($po->source_data) ? $po->source_data : json_decode($po->source_data, true);
        if (isset($data['item_decisions'])) {
            echo "  item_decisions: " . json_encode($data['item_decisions']) . "\n";
        }
        if (isset($data['nominal_rejected'])) {
            echo "  nominal_rejected: Rp " . number_format($data['nominal_rejected'], 0, ',', '.') . "\n";
        }
    }
}

echo "\n=== All PO Status Summary ===\n";
$summary = PurchaseOrder::selectRaw('status, COUNT(*) as count, SUM(total_harga) as total')
    ->groupBy('status')
    ->get();

foreach ($summary as $row) {
    echo "{$row->status}: {$row->count} PO (Total: Rp " . number_format($row->total, 0, ',', '.') . ")\n";
}

echo "\n=== PO Ditolak Details ===\n";
$ditolakList = PurchaseOrder::where('status', 'Ditolak')
    ->select('id', 'status', 'total_harga', 'tanggal_po', 'created_at')
    ->get();

foreach ($ditolakList as $po) {
    echo "PO #{$po->id} - Status: {$po->status} - Total: Rp " . number_format($po->total_harga, 0, ',', '.') . "\n";
    echo "  tanggal_po: " . ($po->tanggal_po ?? 'NULL') . "\n";
    echo "  created_at: " . ($po->created_at ?? 'NULL') . "\n";
}
