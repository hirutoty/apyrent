<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE service_incidents MODIFY COLUMN status ENUM('proses','selesai','tidak_aktif') NOT NULL DEFAULT 'proses'");
    }

    public function down(): void
    {
        DB::statement("UPDATE service_incidents SET status = 'proses' WHERE status = 'tidak_aktif'");
        DB::statement("ALTER TABLE service_incidents MODIFY COLUMN status ENUM('proses','selesai') NOT NULL DEFAULT 'proses'");
    }
};
