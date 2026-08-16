<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL: ALTER TABLE MODIFY COLUMN untuk ubah enum
        DB::statement("ALTER TABLE service_history MODIFY COLUMN status ENUM('proses','selesai','limit') NOT NULL DEFAULT 'proses'");
    }

    public function down(): void
    {
        // Revert: hapus nilai limit (record yang sudah limit akan di-set proses)
        DB::statement("UPDATE service_history SET status = 'proses' WHERE status = 'limit'");
        DB::statement("ALTER TABLE service_history MODIFY COLUMN status ENUM('proses','selesai') NOT NULL DEFAULT 'proses'");
    }
};
