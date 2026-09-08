<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ubah enum persetujuan: tambah 'Pending'
        DB::statement("ALTER TABLE gps_kendaraan MODIFY COLUMN persetujuan ENUM('Pending','Disetujui','Ditolak') NULL DEFAULT NULL");

        // 2. Tambah kolom pembayaran_id
        Schema::table('gps_kendaraan', function (Blueprint $table) {
            $table->unsignedBigInteger('pembayaran_id')->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('gps_kendaraan', function (Blueprint $table) {
            $table->dropColumn('pembayaran_id');
        });

        DB::statement("ALTER TABLE gps_kendaraan MODIFY COLUMN persetujuan ENUM('Disetujui','Ditolak') NULL DEFAULT NULL");
    }
};
