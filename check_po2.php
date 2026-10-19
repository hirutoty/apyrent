<?php
define('LARAVEL_START', microtime(true));
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$po = App\Models\PurchaseOrder::where('po_id', 'PO-2026-001')->first();
if ($po) {
    $sd = $po->source_data;
    echo json_encode([
        'po_id'               => $po->po_id,
        'status'              => $po->status,
        'total_harga'         => $po->total_harga,
        'item_decisions'      => $sd['item_decisions'] ?? [],
        'locked_approved_idx' => $sd['locked_approved_idx'] ?? [],
        'gps_count'           => count($sd['gps_items'] ?? []),
        'nominal_approved_locked' => $sd['nominal_approved_locked'] ?? null,
    ], JSON_PRETTY_PRINT);
}
