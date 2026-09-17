<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ubah ENUM service_parts.status:
     *   - Tambah 'aktif'  (part sudah dibayar, belum dipasang fisik)
     *   - Hapus 'Proses'
     *   - Pertahankan 'Terpasang', 'Limit', 'Diganti', 'tidak_aktif'
     */
    public function up(): void
    {
        // Migrasi data lama: Proses → aktif
        DB::statement("UPDATE service_parts SET status = 'aktif' WHERE status = 'Proses'");

        DB::statement("ALTER TABLE service_parts MODIFY COLUMN status
            ENUM('Terpasang','Limit','Diganti','aktif','tidak_aktif')
            NOT NULL DEFAULT 'tidak_aktif'");
    }

    public function down(): void
    {
        DB::statement("UPDATE service_parts SET status = 'Proses' WHERE status = 'aktif'");

        DB::statement("ALTER TABLE service_parts MODIFY COLUMN status
            ENUM('Terpasang','Limit','Diganti','Proses','tidak_aktif')
            NOT NULL DEFAULT 'Terpasang'");
    }
};
