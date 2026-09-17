<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom pembayaran_id ke tabel service_history.
     * Digunakan sebagai FK untuk tracking pembayaran mana yang menghasilkan
     * service history ini (fase draft tidak_aktif → aktif).
     */
    public function up(): void
    {
        Schema::table('service_history', function (Blueprint $table) {
            if (!Schema::hasColumn('service_history', 'pembayaran_id')) {
                $table->unsignedBigInteger('pembayaran_id')
                      ->nullable()
                      ->after('is_request')
                      ->comment('FK ke pembayarans.id — diisi saat approve PO (draft tidak_aktif)');

                $table->foreign('pembayaran_id')
                      ->references('id')
                      ->on('pembayarans')
                      ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_history', function (Blueprint $table) {
            if (Schema::hasColumn('service_history', 'pembayaran_id')) {
                $table->dropForeign(['pembayaran_id']);
                $table->dropColumn('pembayaran_id');
            }
        });
    }
};
