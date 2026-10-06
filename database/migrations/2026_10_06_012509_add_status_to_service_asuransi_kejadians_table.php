<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom status & catatan_penolakan di service_asuransi_kejadians.
     *
     * status:
     *   diajukan  → default, belum ada keputusan
     *   disetujui → approved di halaman pembayaran, bukti_bayar terisi
     *   ditolak   → rejected di halaman pembayaran, bukti_bayar kosong/null
     */
    public function up(): void
    {
        Schema::table('service_asuransi_kejadians', function (Blueprint $table) {
            $table->string('status', 30)->default('diajukan')->after('bukti_bayar')
                  ->comment('diajukan | disetujui | ditolak');
            $table->text('catatan_penolakan')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('service_asuransi_kejadians', function (Blueprint $table) {
            $table->dropColumn(['status', 'catatan_penolakan']);
        });
    }
};
