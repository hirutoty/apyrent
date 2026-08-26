<?php
/**
 * Debug Script: Check Kontrak Data
 * Run: php debug_kontrak.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "╔══════════════════════════════════════════════════════════════════╗\n";
echo "║           DEBUG: inv_kontraks - Perwakilan Data Check            ║\n";
echo "╚══════════════════════════════════════════════════════════════════╝\n\n";

$kontraks = DB::table('inv_kontraks')
    ->select('id', 'no_kontrak', 'pihak_kedua', 'jenis_pelanggan', 'perwakilan_pihak_kedua', 'jabatan_pihak_kedua', 'status')
    ->orderBy('id', 'desc')
    ->limit(5)
    ->get();

if ($kontraks->isEmpty()) {
    echo "❌ No kontrak data found in database.\n";
    exit;
}

echo "📋 Last 5 Kontrak Records:\n";
echo str_repeat("─", 140) . "\n";
printf("%-5s %-20s %-30s %-15s %-20s %-20s %-10s\n", 
    "ID", "No. Kontrak", "Pihak Kedua", "Jenis", "Perwakilan", "Jabatan", "Status");
echo str_repeat("─", 140) . "\n";

foreach ($kontraks as $k) {
    printf("%-5s %-20s %-30s %-15s %-20s %-20s %-10s\n",
        $k->id,
        $k->no_kontrak ?? 'NULL',
        substr($k->pihak_kedua ?? 'NULL', 0, 30),
        $k->jenis_pelanggan ?? 'NULL',
        $k->perwakilan_pihak_kedua ?? 'NULL',
        $k->jabatan_pihak_kedua ?? 'NULL',
        $k->status ?? 'NULL'
    );
}

echo str_repeat("─", 140) . "\n\n";

// Count kontrak by jenis_pelanggan
$stats = DB::table('inv_kontraks')
    ->selectRaw("jenis_pelanggan, COUNT(*) as total")
    ->groupBy('jenis_pelanggan')
    ->get();

echo "📊 Statistics by Jenis Pelanggan:\n";
foreach ($stats as $stat) {
    $jenis = $stat->jenis_pelanggan ?? 'NULL/Empty';
    echo "  • {$jenis}: {$stat->total} kontrak\n";
}

// Check kontrak with perwakilan data
$withPerwakilan = DB::table('inv_kontraks')
    ->whereNotNull('perwakilan_pihak_kedua')
    ->where('perwakilan_pihak_kedua', '!=', '')
    ->count();

echo "\n✅ Kontrak with perwakilan data: {$withPerwakilan}\n";

echo "\n" . str_repeat("═", 70) . "\n";
echo "Done! If perwakilan fields are NULL, data was not saved properly.\n";
echo str_repeat("═", 70) . "\n";
