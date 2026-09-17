<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah limit_km dari int(11) → bigint agar bisa menampung nilai besar seperti 9999999999
        DB::statement("ALTER TABLE `service_category_limits` MODIFY COLUMN `limit_km` BIGINT NULL COMMENT 'Batas KM per penggantian part dalam kategori ini (opsional)'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `service_category_limits` MODIFY COLUMN `limit_km` INT NULL COMMENT 'Batas KM per penggantian part dalam kategori ini (opsional)'");
    }
};
