<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pembayaran;

echo "=== Check Pembayaran Status Ditolak ===\n";
$ditolak = Pembayaran::where('status', 'Ditolak')->count();
echo "Pembayaran dengan status 'Ditolak': " . $ditolak . "\n";

echo "\n=== Pembayaran Ditolak Details ===\n";
$ditolakList = Pembayaran::where('status', 'Ditolak')
    ->select('id', 'status', 'nominal', 'tanggal', 'created_at')
    ->get();

foreach ($ditolakList as $p) {
    echo "Pembayaran #{$p->id} - Status: {$p->status} - Nominal: Rp " . number_format($p->nominal, 0, ',', '.') . "\n";
    echo "  tanggal: " . ($p->tanggal ?? 'NULL') . "\n";
    echo "  created_at: " . ($p->created_at ?? 'NULL') . "\n";
}

echo "\n=== All Pembayaran Status Summary ===\n";
$summary = Pembayaran::selectRaw('status, COUNT(*) as count, SUM(nominal) as total')
    ->groupBy('status')
    ->get();

foreach ($summary as $row) {
    echo "{$row->status}: {$row->count} Pembayaran (Total: Rp " . number_format($row->total, 0, ',', '.') . ")\n";
}

echo "\n=== Pembayaran Per Month (2026) ===\n";
for ($m = 1; $m <= 12; $m++) {
    $count = Pembayaran::whereMonth('tanggal', $m)->whereYear('tanggal', 2026)->count();
    if ($count > 0) {
        $total = Pembayaran::whereMonth('tanggal', $m)->whereYear('tanggal', 2026)->sum('nominal');
        $ditolakCount = Pembayaran::where('status', 'Ditolak')
            ->whereMonth('tanggal', $m)
            ->whereYear('tanggal', 2026)
            ->count();
        $ditolakTotal = Pembayaran::where('status', 'Ditolak')
            ->whereMonth('tanggal', $m)
            ->whereYear('tanggal', 2026)
            ->sum('nominal');
        
        $monthName = date('F', mktime(0, 0, 0, $m, 1));
        echo "Month $m ($monthName): $count pembayaran (Total: Rp " . number_format($total, 0, ',', '.') . ")";
        if ($ditolakCount > 0) {
            echo " - DITOLAK: $ditolakCount pembayaran (Rp " . number_format($ditolakTotal, 0, ',', '.') . ")";
        }
        echo "\n";
    }
}
