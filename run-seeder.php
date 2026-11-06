<?php

// Bootstrap Laravel
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Jalankan seeder
echo "Menjalankan DataLeasingSeeder...\n\n";

try {
    $seeder = new Database\Seeders\DataLeasingSeeder();
    $seeder->setCommand(new class {
        public function info($message) {
            echo "[INFO] $message\n";
        }
        public function warn($message) {
            echo "[WARN] $message\n";
        }
        public function error($message) {
            echo "[ERROR] $message\n";
        }
    });
    
    $seeder->run();
    
    echo "\n✅ Seeder berhasil dijalankan!\n";
    
    // Hitung total data
    $total = App\Models\DataLeasing::count();
    echo "Total data leasing sekarang: $total\n";
    
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
