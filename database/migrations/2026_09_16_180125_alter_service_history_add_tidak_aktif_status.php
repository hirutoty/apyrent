<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE service_history MODIFY COLUMN status ENUM('proses','selesai','limit','tidak_aktif') NOT NULL DEFAULT 'proses'");
    }

    public function down(): void
    {
        DB::statement("UPDATE service_history SET status = 'proses' WHERE status = 'tidak_aktif'");
        DB::statement("ALTER TABLE service_history MODIFY COLUMN status ENUM('proses','selesai','limit') NOT NULL DEFAULT 'proses'");
    }
};
