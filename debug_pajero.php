<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Cari kendaraan berdasarkan nopol
$kendaraan = \App\Models\Kendaraan::where('nopol', 'AB 1132 MP')->first();

if (!$kendaraan) {
    echo "❌ Kendaraan AB 1132 MP tidak ditemukan!\n";
    exit;
}

echo "🚗 Kendaraan: {$kendaraan->nopol} - {$kendaraan->nama_kendaraan} (ID: {$kendaraan->id})\n";
echo str_repeat('=', 100) . "\n\n";

// Cek ceklist terakhir
echo "📋 CEKLIST KONDISI:\n";
$ceklists = $kendaraan->ceklistKondisi()->orderBy('created_at', 'desc')->get();
if ($ceklists->isEmpty()) {
    echo "   ❌ Belum ada ceklist\n";
} else {
    foreach ($ceklists as $index => $c) {
        echo "   " . ($index + 1) . ". Created: {$c->created_at}\n";
    }
}
echo "\n";

// Cek SEMUA service parts
echo "🔧 SEMUA SERVICE PARTS:\n";
$parts = $kendaraan->serviceParts()
    ->select('id', 'nama_part', 'persetujuan', 'tanggal_approval', 'created_at')
    ->orderBy('created_at', 'desc')
    ->get();

if ($parts->isEmpty()) {
    echo "   ❌ Tidak ada service parts\n";
} else {
    foreach ($parts as $index => $part) {
        echo "\n   Part #" . ($index + 1) . ":\n";
        echo "   - ID: {$part->id}\n";
        echo "   - Nama: {$part->nama_part}\n";
        echo "   - Persetujuan: " . ($part->persetujuan ?? 'NULL') . "\n";
        
        if ($part->tanggal_approval) {
            echo "   - Tanggal Approval: ✅ {$part->tanggal_approval} (ADA!)\n";
        } else {
            echo "   - Tanggal Approval: ❌ NULL\n";
        }
        
        echo "   - Created: {$part->created_at}\n";
    }
}

echo "\n" . str_repeat('=', 100) . "\n";
echo "🎯 HASIL:\n";
$shouldShow = $kendaraan->shouldShowCeklistButton();
echo $shouldShow ? "   ✅ TOMBOL MUNCUL\n" : "   ❌ TOMBOL HILANG\n";

if ($shouldShow && !$ceklists->isEmpty()) {
    echo "\n💡 Tombol muncul karena ada part dengan tanggal_approval SETELAH ceklist terakhir!\n";
} elseif ($shouldShow && $ceklists->isEmpty()) {
    echo "\n💡 Tombol muncul karena belum pernah ada ceklist!\n";
}
