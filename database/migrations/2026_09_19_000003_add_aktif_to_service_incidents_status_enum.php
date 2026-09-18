<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah 'aktif' ke enum status service_incidents
        DB::statement("ALTER TABLE `service_incidents`
            MODIFY COLUMN `status` ENUM('proses','selesai','tidak_aktif','aktif')
            NOT NULL DEFAULT 'proses'");
    }

    public function down(): void
    {
        // Kembalikan ke enum semula (update data dulu)
        DB::statement("UPDATE `service_incidents` SET `status` = 'proses' WHERE `status` = 'aktif'");
        DB::statement("ALTER TABLE `service_incidents`
            MODIFY COLUMN `status` ENUM('proses','selesai','tidak_aktif')
            NOT NULL DEFAULT 'proses'");
    }
};
