<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom baru
        Schema::table('inv_kontraks', function (Blueprint $table) {
            $table->unsignedInteger('durasi_value')->nullable()->after('perjanjian_pembayaran');
            $table->string('durasi_satuan', 10)->nullable()->after('durasi_value'); // hari/bulan/tahun
            $table->date('tanggal_selesai')->nullable()->after('durasi_satuan');
            $table->text('file_draft')->nullable()->after('file_persyaratan');
        });

        // Ubah enum status: tambah 'selesai-belum lunas'
        DB::statement("ALTER TABLE inv_kontraks MODIFY COLUMN status ENUM(
            'dibuat','pending','approved','active','rejected','expired',
            'completed','terminated','selesai-belum lunas'
        ) DEFAULT 'pending'");
    }

    public function down(): void
    {
        Schema::table('inv_kontraks', function (Blueprint $table) {
            $table->dropColumn(['durasi_value', 'durasi_satuan', 'tanggal_selesai', 'file_draft']);
        });

        DB::statement("ALTER TABLE inv_kontraks MODIFY COLUMN status ENUM(
            'dibuat','pending','approved','active','rejected','expired',
            'completed','terminated'
        ) DEFAULT 'pending'");
    }
};
