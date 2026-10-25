<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah nilai 'hilang' ke enum status_kendaraan di tabel kendaraan
        DB::statement("ALTER TABLE kendaraan MODIFY COLUMN status_kendaraan ENUM('tersedia','disewa','service','bermasalah','hilang') NOT NULL DEFAULT 'tersedia'");
    }

    public function down(): void
    {
        // Kembalikan enum tanpa 'hilang' (pastikan tidak ada data 'hilang' sebelum rollback)
        DB::statement("ALTER TABLE kendaraan MODIFY COLUMN status_kendaraan ENUM('tersedia','disewa','service','bermasalah') NOT NULL DEFAULT 'tersedia'");
    }
};
