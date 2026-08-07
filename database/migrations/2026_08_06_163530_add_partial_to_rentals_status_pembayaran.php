<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah 'partial' ke enum status_pembayaran di tabel rentals
        DB::statement("ALTER TABLE rentals MODIFY COLUMN status_pembayaran ENUM('belum_bayar','dp','partial','lunas') NOT NULL DEFAULT 'belum_bayar'");
    }

    public function down(): void
    {
        // Rollback: hapus 'partial', data yang punya nilai partial dikembalikan ke 'belum_bayar'
        DB::statement("UPDATE rentals SET status_pembayaran = 'belum_bayar' WHERE status_pembayaran = 'partial'");
        DB::statement("ALTER TABLE rentals MODIFY COLUMN status_pembayaran ENUM('belum_bayar','dp','lunas') NOT NULL DEFAULT 'belum_bayar'");
    }
};
