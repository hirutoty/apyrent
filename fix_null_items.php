<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Hapus item penawaran yang kendaraan_id-nya null
$deleted = \App\Models\InvPenawaranItem::whereNull('kendaraan_id')->delete();
echo "Deleted null kendaraan_id items: {$deleted}\n";
echo "Remaining items: " . \App\Models\InvPenawaranItem::count() . "\n";

// Juga hapus penawaran yang sudah tidak punya item sama sekali (orphan)
$orphan = \App\Models\InvPenawaran::doesntHave('items')->count();
echo "Penawaran tanpa item: {$orphan}\n";
