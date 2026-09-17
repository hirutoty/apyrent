<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tambah nilai 'tidak_aktif' ke enum status di service_asuransi.
     * MySQL tidak mendukung alter enum langsung via Blueprint,
     * jadi ubah kolom menjadi VARCHAR dulu lalu back ke enum.
     */
    public function up(): void
    {
        // Ubah ke string dulu agar bisa menambah nilai enum
        DB::statement("ALTER TABLE service_asuransi MODIFY COLUMN status ENUM('bermasalah','selesai','tidak_aktif') NOT NULL DEFAULT 'bermasalah'");
    }

    public function down(): void
    {
        // Rollback: hapus nilai tidak_aktif (data yang sudah tidak_aktif diubah ke bermasalah)
        DB::statement("UPDATE service_asuransi SET status = 'bermasalah' WHERE status = 'tidak_aktif'");
        DB::statement("ALTER TABLE service_asuransi MODIFY COLUMN status ENUM('bermasalah','selesai') NOT NULL DEFAULT 'bermasalah'");
    }
};
