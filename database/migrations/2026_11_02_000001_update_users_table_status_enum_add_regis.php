<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Untuk MySQL: Ubah enum status menjadi 'regis', 'aktif', 'blokir'
        DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('regis', 'aktif', 'blokir') DEFAULT 'regis'");
        
        // Update role enum untuk menambahkan role yang kurang
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'keuangan', 'produksi', 'hrd', 'purchase', 'sales', 'marketing', 'it') NULL DEFAULT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback ke enum lama
        DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('aktif', 'blokir') DEFAULT 'aktif'");
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'keuangan', 'produksi') DEFAULT 'produksi'");
    }
};
