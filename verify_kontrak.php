<?php
$files = [
    'kontrak-generator/package.json',
    'kontrak-generator/src/schema.js',
    'kontrak-generator/src/coordinates.js',
    'kontrak-generator/src/generateContract.js',
    'kontrak-generator/examples/sample-data.json',
    'kontrak-generator/scripts/visualDiff.js',
    'kontrak-generator/README.md',
    'kontrak-generator/assets/template-kontrak.pdf',
    'app/Http/Controllers/Admin/InvKontrakController.php',
];
foreach ($files as $f) {
    $exists = file_exists($f);
    $size   = $exists ? number_format(filesize($f)) . ' bytes' : '-';
    echo ($exists ? 'OK' : 'MISSING') . "  $f  ($size)\n";
}
