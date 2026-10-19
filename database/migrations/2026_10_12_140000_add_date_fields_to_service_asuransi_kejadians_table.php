<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom date fields ke service_asuransi_kejadians.
     * 
     * Columns:
     *   - tanggal: tanggal service (diambil dari parent service_asuransi)
     *   - periode_mulai: awal periode asuransi
     *   - periode_selesai: akhir periode asuransi
     *   - tanggal_approval: timestamp saat kejadian di-approve (NULL jika belum/ditolak)
     */
    public function up(): void
    {
        Schema::table('service_asuransi_kejadians', function (Blueprint $table) {
            $table->date('tanggal')->nullable()->after('service_asuransi_id')
                  ->comment('Tanggal service (copied from parent service_asuransi)');
            
            $table->date('periode_mulai')->nullable()->after('tanggal')
                  ->comment('Awal periode asuransi');
            
            $table->date('periode_selesai')->nullable()->after('periode_mulai')
                  ->comment('Akhir periode asuransi');
            
            $table->datetime('tanggal_approval')->nullable()->after('status')
                  ->comment('Timestamp saat kejadian di-approve (NULL jika belum/ditolak)');
        });
    }

    public function down(): void
    {
        Schema::table('service_asuransi_kejadians', function (Blueprint $table) {
            $table->dropColumn(['tanggal', 'periode_mulai', 'periode_selesai', 'tanggal_approval']);
        });
    }
};
