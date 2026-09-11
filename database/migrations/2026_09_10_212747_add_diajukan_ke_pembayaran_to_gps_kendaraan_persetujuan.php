<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah nilai 'Diajukan ke Pembayaran' ke enum persetujuan
        DB::statement("ALTER TABLE gps_kendaraan MODIFY COLUMN persetujuan ENUM('Pending','Diajukan ke Pembayaran','Disetujui','Ditolak') NULL DEFAULT NULL");
    }

    public function down(): void
    {
        // Rollback: hapus nilai baru (record yang sudah pakai nilai ini akan NULL)
        DB::statement("UPDATE gps_kendaraan SET persetujuan = NULL WHERE persetujuan = 'Diajukan ke Pembayaran'");
        DB::statement("ALTER TABLE gps_kendaraan MODIFY COLUMN persetujuan ENUM('Pending','Disetujui','Ditolak') NULL DEFAULT NULL");
    }
};
