<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom bukti_pembayaran ke service_parts.
     * Menyimpan path file bukti bayar yang diupload saat approve pembayaran.
     * Nullable JSON — satu part bisa punya beberapa file bukti bayar.
     */
    public function up(): void
    {
        Schema::table('service_parts', function (Blueprint $table) {
            $table->json('bukti_pembayaran')
                  ->nullable()
                  ->after('bukti')
                  ->comment('File bukti bayar per-part dari approval pembayaran');
        });
    }

    public function down(): void
    {
        Schema::table('service_parts', function (Blueprint $table) {
            $table->dropColumn('bukti_pembayaran');
        });
    }
};
