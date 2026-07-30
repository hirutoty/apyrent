<?php
// Task 6: Hapus modal Tambah dari payments/index.blade.php

$f = __DIR__ . '/resources/views/admin/payments/index.blade.php';
$c = file_get_contents($f);

// Penanda awal dan akhir blok modal tambah
$startMarker = '{{-- ================================================================
     MODAL TAMBAH
================================================================ --}}';
$endMarker   = '{{-- ================================================================
     MODAL EDIT
================================================================ --}}';

$startPos = strpos($c, $startMarker);
$endPos   = strpos($c, $endMarker);

if ($startPos !== false && $endPos !== false && $endPos > $startPos) {
    $newC = substr($c, 0, $startPos) . substr($c, $endPos);
    file_put_contents($f, $newC);
    echo "OK: Modal Tambah dihapus (" . ($endPos - $startPos) . " chars)\n";
} else {
    // Fallback: cari dengan pola berbeda
    $startMarker2 = "{{-- ================================================================\r\n     MODAL TAMBAH";
    $endMarker2   = "{{-- ================================================================\r\n     MODAL EDIT";
    $startPos = strpos($c, $startMarker2);
    $endPos   = strpos($c, $endMarker2);
    if ($startPos !== false && $endPos !== false && $endPos > $startPos) {
        $newC = substr($c, 0, $startPos) . substr($c, $endPos);
        file_put_contents($f, $newC);
        echo "OK (CRLF): Modal Tambah dihapus (" . ($endPos - $startPos) . " chars)\n";
    } else {
        echo "WARN: marker tidak ditemukan, coba regex\n";
        // Regex fallback
        $newC = preg_replace('/\{\{--\s*={10,}.*?MODAL TAMBAH.*?={10,}.*?--\}\}.*?\{\{--\s*={10,}.*?MODAL EDIT/su',
            '{{-- ================================================================' . "\n" .
            '     MODAL EDIT',
            $c);
        if ($newC && $newC !== $c) {
            file_put_contents($f, $newC);
            echo "OK (regex): Modal Tambah dihapus\n";
        } else {
            echo "FAIL: tidak bisa hapus modal tambah\n";
            // Cek apakah modalTambah masih ada
            echo "modalTambah div: " . (strpos($c, 'id="modalTambah"') !== false ? 'MASIH ADA' : 'SUDAH TIDAK ADA') . "\n";
        }
    }
}

// Cek hasil
$result = file_get_contents($f);
echo "modalTambah div setelah proses: " . (strpos($result, 'id="modalTambah"') !== false ? 'MASIH ADA' : 'SUDAH TIDAK ADA') . "\n";
echo "modalEdit div: " . (strpos($result, 'id="modalEdit"') !== false ? 'ADA' : 'TIDAK ADA') . "\n";
