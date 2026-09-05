<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            // Source type untuk identifikasi jenis pengeluaran
            // Values: 'asuransi_kendaraan', 'pajak', 'service_part', 'gps', 'kir', 'stnk', 'service_asuransi'
            $table->string('source_type', 50)->nullable()->after('status')
                ->comment('Jenis pengeluaran: asuransi_kendaraan, pajak, service_part, gps, kir, stnk, service_asuransi');
            
            // Source data menyimpan semua field dari form original dalam format JSON
            $table->json('source_data')->nullable()->after('source_type')
                ->comment('All fields from original form in JSON format');
            
            // Target ID untuk link ke record di tabel tujuan setelah approved
            $table->unsignedBigInteger('target_id')->nullable()->after('source_data')
                ->comment('ID record di tabel tujuan setelah approved');
            
            // Flag untuk allow/disallow edit (TRUE jika ditolak dan bisa edit ulang)
            $table->boolean('can_edit')->default(false)->after('target_id')
                ->comment('Flag untuk allow/disallow edit');
            
            // Add indexes untuk performa query
            $table->index('source_type', 'idx_pembayarans_source_type');
            $table->index('target_id', 'idx_pembayarans_target_id');
            $table->index(['status', 'source_type'], 'idx_pembayarans_status_source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_pembayarans_source_type');
            $table->dropIndex('idx_pembayarans_target_id');
            $table->dropIndex('idx_pembayarans_status_source');
            
            // Drop columns
            $table->dropColumn(['source_type', 'source_data', 'target_id', 'can_edit']);
        });
    }
};
