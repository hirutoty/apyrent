<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Langkah 1: ubah ENUM untuk menambah nilai 'expired' (sementara dua-duanya ada)
        DB::statement("ALTER TABLE gps_kendaraan MODIFY COLUMN status_sewa ENUM('aktif','habis','expired','tidak_aktif') NOT NULL DEFAULT 'aktif'");

        // Langkah 2: migrate data lama habis → expired
        DB::statement("UPDATE gps_kendaraan SET status_sewa = 'expired' WHERE status_sewa = 'habis'");

        // Langkah 3: hapus nilai 'habis' dari ENUM
        DB::statement("ALTER TABLE gps_kendaraan MODIFY COLUMN status_sewa ENUM('aktif','expired','tidak_aktif') NOT NULL DEFAULT 'aktif'");
    }

    public function down(): void
    {
        // Rollback: tambah 'habis' kembali
        DB::statement("ALTER TABLE gps_kendaraan MODIFY COLUMN status_sewa ENUM('aktif','habis','expired','tidak_aktif') NOT NULL DEFAULT 'aktif'");
        DB::statement("UPDATE gps_kendaraan SET status_sewa = 'habis' WHERE status_sewa = 'expired'");
        DB::statement("ALTER TABLE gps_kendaraan MODIFY COLUMN status_sewa ENUM('aktif','habis','tidak_aktif') NOT NULL DEFAULT 'aktif'");
    }
};
