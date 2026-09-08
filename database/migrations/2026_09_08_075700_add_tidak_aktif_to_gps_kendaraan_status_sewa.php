<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE gps_kendaraan MODIFY COLUMN status_sewa ENUM('aktif','habis','tidak_aktif') NOT NULL DEFAULT 'aktif'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE gps_kendaraan MODIFY COLUMN status_sewa ENUM('aktif','habis') NOT NULL DEFAULT 'aktif'");
    }
};
