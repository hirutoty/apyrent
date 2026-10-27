<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔄 RESET TANGGAL APPROVAL...\n";
echo str_repeat('=', 80) . "\n";

// Reset semua tanggal_approval ke NULL
$updated = \DB::table('service_parts')
    ->whereNotNull('tanggal_approval')
    ->update(['tanggal_approval' => null]);

echo "✅ Berhasil reset {$updated} service parts\n";
echo "   Semua tanggal_approval sekarang NULL\n\n";

echo "💡 Sekarang workflow fresh:\n";
echo "   1. Approve PO → tanggal_approval tetap NULL, tombol ceklist TIDAK muncul\n";
echo "   2. Approve Pembayaran → tanggal_approval diisi, tombol ceklist MUNCUL\n";
echo "   3. Submit ceklist → tombol HILANG\n";
echo "   4. Approve pembayaran lagi → tombol MUNCUL lagi\n";
