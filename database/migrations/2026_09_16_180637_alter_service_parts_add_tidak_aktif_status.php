<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE service_parts MODIFY COLUMN status ENUM('Terpasang','Limit','Diganti','Proses','tidak_aktif') NOT NULL DEFAULT 'Terpasang'");
    }

    public function down(): void
    {
        DB::statement("UPDATE service_parts SET status = 'Proses' WHERE status = 'tidak_aktif'");
        DB::statement("ALTER TABLE service_parts MODIFY COLUMN status ENUM('Terpasang','Limit','Diganti','Proses') NOT NULL DEFAULT 'Terpasang'");
    }
};
