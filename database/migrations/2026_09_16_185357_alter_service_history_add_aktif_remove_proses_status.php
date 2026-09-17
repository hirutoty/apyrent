<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ubah ENUM service_history.status:
     *   - Tambah 'aktif'  (menggantikan 'proses' — payment disetujui, belum terpasang fisik)
     *   - Hapus 'proses'
     *   - Tambah 'terpasang' (part sudah dipasang secara fisik)
     *   - Pertahankan 'selesai', 'limit', 'tidak_aktif'
     * DEFAULT diubah ke 'tidak_aktif' supaya draft PO langsung masuk status benar.
     */
    public function up(): void
    {
        // Migrasi data lama: proses → aktif
        DB::statement("UPDATE service_history SET status = 'aktif' WHERE status = 'proses'");

        DB::statement("ALTER TABLE service_history MODIFY COLUMN status
            ENUM('aktif','terpasang','selesai','limit','tidak_aktif')
            NOT NULL DEFAULT 'tidak_aktif'");
    }

    public function down(): void
    {
        DB::statement("UPDATE service_history SET status = 'proses' WHERE status = 'aktif'");
        DB::statement("UPDATE service_history SET status = 'selesai' WHERE status = 'terpasang'");

        DB::statement("ALTER TABLE service_history MODIFY COLUMN status
            ENUM('proses','selesai','limit','tidak_aktif')
            NOT NULL DEFAULT 'proses'");
    }
};
