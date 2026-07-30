<?php
$f = __DIR__ . '/resources/views/admin/summary/index.blade.php';
$c = file_get_contents($f);

// Hapus blok modal Tambah HTML (dari marker pembuka sampai sebelum modal Edit)
$patterns = [
    // Pola 1: CRLF
    "{{-- ================================================================\r\n     MODAL TAMBAH\r\n================================================================ --}}",
    // Pola 2: LF
    "{{-- ================================================================\n     MODAL TAMBAH\n================================================================ --}}",
];
$endMarkers = [
    "{{-- ================================================================\r\n     MODAL EDIT\r\n================================================================ --}}",
    "{{-- ================================================================\n     MODAL EDIT\n================================================================ --}}",
];

$found = false;
foreach ($patterns as $i => $startMarker) {
    $startPos = strpos($c, $startMarker);
    $endPos   = strpos($c, $endMarkers[$i]);
    if ($startPos !== false && $endPos !== false && $endPos > $startPos) {
        $c = substr($c, 0, $startPos) . substr($c, $endPos);
        $found = true;
        echo "OK: Modal Tambah HTML dihapus (" . ($endPos - $startPos) . " chars, pola " . ($i+1) . ")\n";
        break;
    }
}

if (!$found) {
    // Regex fallback
    $newC = preg_replace(
        '/\{\{--\s*=+\s*\r?\nMODAL TAMBAH\r?\n\s*=+\s*--\}\}.*?\{\{--\s*=+\s*\r?\nMODAL EDIT/su',
        '{{-- ================================================================' . "\n" . '     MODAL EDIT',
        $c
    );
    if ($newC && strlen($newC) < strlen($c)) {
        $c = $newC;
        echo "OK (regex): Modal Tambah HTML dihapus\n";
        $found = true;
    }
}

if (!$found) {
    echo "INFO: Modal Tambah HTML sudah tidak ada atau marker berbeda\n";
    // Cek apakah modalTambah masih ada
    echo "id=modalTambah: " . (strpos($c, 'id="modalTambah"') !== false ? 'MASIH ADA' : 'SUDAH TIDAK ADA') . "\n";
} else {
    file_put_contents($f, $c);
}

// Final check
$result = file_get_contents($f);
echo "\n=== FINAL CHECK ===\n";
echo "id=modalTambah: " . (strpos($result, 'id="modalTambah"') !== false ? 'MASIH ADA' : 'SUDAH TIDAK ADA') . "\n";
echo "openModalTambah: " . (strpos($result, 'openModalTambah') !== false ? 'MASIH ADA' : 'SUDAH TIDAK ADA') . "\n";
echo "id=modalEdit: " . (strpos($result, 'id="modalEdit"') !== false ? 'ADA' : 'TIDAK ADA') . "\n";
