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
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Source tracking fields
            $table->string('source_type')->nullable()->after('catatan')->comment('Type: gps, asuransi_kendaraan, pajak, etc');
            $table->json('source_data')->nullable()->after('source_type')->comment('Raw form data from submission');
            
            // Approval workflow fields
            $table->enum('status', ['Pending', 'Disetujui', 'Ditolak'])->default('Pending')->after('source_data');
            $table->unsignedBigInteger('disetujui_oleh')->nullable()->after('status');
            $table->datetime('tanggal_persetujuan')->nullable()->after('disetujui_oleh');
            $table->text('catatan_approval')->nullable()->after('tanggal_persetujuan')->comment('Approval notes from admin');
            
            // Link to Pembayaran (created after PO approved)
            $table->unsignedBigInteger('pembayaran_id')->nullable()->after('catatan_approval');
            
            // Edit capability flag
            $table->boolean('can_edit')->default(false)->after('pembayaran_id');
            
            // Resubmit tracking
            $table->datetime('terakhir_diajukan')->nullable()->after('can_edit');
            
            // Foreign keys
            $table->foreign('disetujui_oleh')->references('id')->on('users')->onDelete('set null');
            $table->foreign('pembayaran_id')->references('id')->on('pembayarans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['disetujui_oleh']);
            $table->dropForeign(['pembayaran_id']);
            
            // Drop columns
            $table->dropColumn([
                'source_type',
                'source_data',
                'status',
                'disetujui_oleh',
                'tanggal_persetujuan',
                'catatan_approval',
                'pembayaran_id',
                'can_edit',
                'terakhir_diajukan',
            ]);
        });
    }
};
