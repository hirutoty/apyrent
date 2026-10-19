<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pembayaran;

echo "=== Fixing Pembayaran #10 Ditolak ===\n";

$pembayaran = Pembayaran::find(10);
if ($pembayaran) {
    echo "Before: Pembayaran #{$pembayaran->id} - Status: {$pembayaran->status} - Nominal: Rp " . number_format($pembayaran->nominal, 0, ',', '.') . "\n";
    
    // Update nominal to 150000
    $pembayaran->nominal = 150000;
    $pembayaran->save();
    
    echo "After: Pembayaran #{$pembayaran->id} - Status: {$pembayaran->status} - Nominal: Rp " . number_format($pembayaran->nominal, 0, ',', '.') . "\n";
    echo "\n✅ Successfully updated Pembayaran #10 nominal to Rp 150.000\n";
} else {
    echo "❌ Pembayaran #10 not found!\n";
}
