<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE service_parts MODIFY COLUMN status ENUM('Terpasang','Limit','Diganti','Proses') NOT NULL DEFAULT 'Terpasang'");
    }

    public function down(): void
    {
        // Kembalikan part Proses ke Terpasang sebelum revert enum
        DB::statement("UPDATE service_parts SET status = 'Terpasang' WHERE status = 'Proses'");
        DB::statement("ALTER TABLE service_parts MODIFY COLUMN status ENUM('Terpasang','Limit','Diganti') NOT NULL DEFAULT 'Terpasang'");
    }
};
