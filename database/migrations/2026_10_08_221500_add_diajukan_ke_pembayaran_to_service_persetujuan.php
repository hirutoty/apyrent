<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah nilai 'Diajukan ke Pembayaran' ke ENUM persetujuan di service_history
        // ENUM sebelumnya: ('Pending','Disetujui','Ditolak','Ditolak Pembayaran')
        DB::statement("ALTER TABLE service_history MODIFY COLUMN persetujuan ENUM('Pending','Diajukan ke Pembayaran','Disetujui','Ditolak','Ditolak Pembayaran') NULL");

        // Tambah nilai 'Diajukan ke Pembayaran' ke ENUM persetujuan di service_parts
        DB::statement("ALTER TABLE service_parts MODIFY COLUMN persetujuan ENUM('Pending','Diajukan ke Pembayaran','Disetujui','Ditolak','Ditolak Pembayaran') NULL");
    }

    public function down(): void
    {
        // Rollback: kembalikan ke ENUM tanpa 'Diajukan ke Pembayaran'
        // Catatan: data yang sudah bernilai 'Diajukan ke Pembayaran' akan menjadi NULL
        DB::statement("ALTER TABLE service_history MODIFY COLUMN persetujuan ENUM('Pending','Disetujui','Ditolak','Ditolak Pembayaran') NULL");
        DB::statement("ALTER TABLE service_parts MODIFY COLUMN persetujuan ENUM('Pending','Disetujui','Ditolak','Ditolak Pembayaran') NULL");
    }
};
