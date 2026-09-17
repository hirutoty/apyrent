<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$parts = DB::table('service_parts')->whereIn('status',['aktif','Terpasang'])->get(['id','nama_part','status','bukti_pembayaran']);
foreach ($parts as $p) {
    $bp = $p->bukti_pembayaran ? json_decode($p->bukti_pembayaran,true) : [];
    $count = count($bp);
    $first = $bp[0] ?? null;
    echo "PART#{$p->id} {$p->nama_part} status={$p->status} bukti_pembayaran={$count} files\n";
    if ($first) echo "  -> " . ($first['original_name'] ?? $first['name'] ?? '?') . " @ {$first['path']}\n";
}
