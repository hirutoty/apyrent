<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah nilai 'Ditolak Pembayaran' ke ENUM persetujuan di service_history
        DB::statement("ALTER TABLE service_history MODIFY COLUMN persetujuan ENUM('Pending','Disetujui','Ditolak','Ditolak Pembayaran') NULL");

        // Tambah nilai 'Ditolak Pembayaran' ke ENUM persetujuan di service_parts
        DB::statement("ALTER TABLE service_parts MODIFY COLUMN persetujuan ENUM('Pending','Disetujui','Ditolak','Ditolak Pembayaran') NULL");
    }

    public function down(): void
    {
        // Rollback: kembalikan ke ENUM tanpa 'Ditolak Pembayaran'
        // (data yang sudah ada nilai 'Ditolak Pembayaran' akan jadi NULL)
        DB::statement("ALTER TABLE service_history MODIFY COLUMN persetujuan ENUM('Pending','Disetujui','Ditolak') NULL");
        DB::statement("ALTER TABLE service_parts MODIFY COLUMN persetujuan ENUM('Pending','Disetujui','Ditolak') NULL");
    }
};
