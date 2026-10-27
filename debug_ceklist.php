<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Ganti ID kendaraan sesuai yang Anda test
$kendaraanId = 1; // GANTI INI SESUAI KENDARAAN YANG ANDA TEST

$kendaraan = \App\Models\Kendaraan::find($kendaraanId);

if (!$kendaraan) {
    echo "❌ Kendaraan ID {$kendaraanId} tidak ditemukan!\n";
    exit;
}

echo "🚗 Kendaraan: {$kendaraan->nopol} (ID: {$kendaraan->id})\n";
echo str_repeat('=', 80) . "\n\n";

// Cek ceklist terakhir
echo "📋 CEKLIST KONDISI TERAKHIR:\n";
$lastCeklist = $kendaraan->ceklistKondisi()->first();
if ($lastCeklist) {
    echo "   ✅ Ada ceklist terakhir\n";
    echo "   📅 Created: {$lastCeklist->created_at}\n";
} else {
    echo "   ❌ Belum ada ceklist\n";
}
echo "\n";

// Cek service parts
echo "🔧 SERVICE PARTS (5 terbaru):\n";
$parts = $kendaraan->serviceParts()
    ->select('id', 'nama_part', 'persetujuan', 'tanggal_approval', 'created_at')
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

if ($parts->isEmpty()) {
    echo "   ❌ Tidak ada service parts\n";
} else {
    foreach ($parts as $index => $part) {
        echo "\n   Part #" . ($index + 1) . ":\n";
        echo "   - Nama: {$part->nama_part}\n";
        echo "   - Persetujuan: " . ($part->persetujuan ?? 'NULL') . "\n";
        echo "   - Tanggal Approval: " . ($part->tanggal_approval ?? '❌ NULL (PO belum di-approve pembayaran)') . "\n";
        echo "   - Created: {$part->created_at}\n";
        
        if ($lastCeklist && $part->tanggal_approval) {
            $isNewer = $part->tanggal_approval > $lastCeklist->created_at;
            echo "   - Lebih baru dari ceklist? " . ($isNewer ? '✅ YA' : '❌ TIDAK') . "\n";
        }
    }
}

echo "\n" . str_repeat('=', 80) . "\n";
echo "🎯 HASIL CEK shouldShowCeklistButton():\n";
$shouldShow = $kendaraan->shouldShowCeklistButton();
echo $shouldShow ? "   ✅ TOMBOL HARUS MUNCUL\n" : "   ❌ TOMBOL HARUS HILANG\n";

echo "\n💡 ANALISIS:\n";
if (!$lastCeklist) {
    echo "   → Tombol muncul karena belum pernah isi ceklist\n";
} else {
    $hasNew = $kendaraan->serviceParts()
        ->where('persetujuan', 'Disetujui')
        ->whereNotNull('tanggal_approval')
        ->where('tanggal_approval', '>', $lastCeklist->created_at)
        ->exists();
    
    if ($hasNew) {
        echo "   → Tombol muncul karena ada part dengan pembayaran di-approve setelah ceklist terakhir\n";
    } else {
        echo "   → Tombol hilang karena tidak ada part baru yang pembayarannya di-approve\n";
    }
}
