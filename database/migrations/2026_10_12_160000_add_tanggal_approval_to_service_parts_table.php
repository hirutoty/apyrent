<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom tanggal_approval ke service_parts.
     * 
     * Column:
     *   - tanggal_approval: timestamp saat part di-approve di pembayaran (NULL jika belum/ditolak)
     */
    public function up(): void
    {
        Schema::table('service_parts', function (Blueprint $table) {
            $table->datetime('tanggal_approval')->nullable()->after('persetujuan')
                  ->comment('Timestamp saat part di-approve di pembayaran (NULL jika belum/ditolak)');
        });
    }

    public function down(): void
    {
        Schema::table('service_parts', function (Blueprint $table) {
            $table->dropColumn('tanggal_approval');
        });
    }
};
