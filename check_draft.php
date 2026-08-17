<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$kontraks = App\Models\InvKontrak::latest()->get(['id','no_kontrak','file_draft']);
foreach ($kontraks as $k) {
    $exists = $k->file_draft ? (file_exists(public_path($k->file_draft)) ? 'FILE ADA' : 'FILE HILANG') : 'NULL';
    echo "ID:{$k->id} | {$k->no_kontrak} | file_draft: " . ($k->file_draft ?? 'NULL') . " | $exists\n";
}
