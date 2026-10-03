<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PR-SIN-002 source_data keys ===" . PHP_EOL;
$pmb = DB::table('pembayarans')->where('id', 2)->first();
if ($pmb) {
    $sd = json_decode($pmb->source_data, true) ?? [];
    echo "kendaraan_id: " . ($sd['kendaraan_id'] ?? 'NULL') . PHP_EOL;
    echo "tanggal_service: " . ($sd['tanggal_service'] ?? 'NULL') . PHP_EOL;
    echo "service_incident_id: " . ($sd['service_incident_id'] ?? 'NULL') . PHP_EOL;
    echo "parts count: " . count($sd['parts'] ?? []) . PHP_EOL;
    echo "Keys: " . implode(', ', array_keys($sd)) . PHP_EOL;
}
