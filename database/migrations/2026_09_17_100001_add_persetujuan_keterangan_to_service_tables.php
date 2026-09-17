<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // service_asuransi: tambah kolom persetujuan
        Schema::table('service_asuransi', function (Blueprint $table) {
            if (!Schema::hasColumn('service_asuransi', 'persetujuan')) {
                $table->string('persetujuan')->nullable()->after('pembayaran_id');
            }
            if (!Schema::hasColumn('service_asuransi', 'purchase_order_id')) {
                $table->unsignedBigInteger('purchase_order_id')->nullable()->after('pembayaran_id');
            }
        });

        // service_incidents: tambah kolom persetujuan + keterangan
        Schema::table('service_incidents', function (Blueprint $table) {
            if (!Schema::hasColumn('service_incidents', 'persetujuan')) {
                $table->string('persetujuan')->nullable()->after('pembayaran_id');
            }
            if (!Schema::hasColumn('service_incidents', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('keluhan');
            }
            if (!Schema::hasColumn('service_incidents', 'purchase_order_id')) {
                $table->unsignedBigInteger('purchase_order_id')->nullable()->after('pembayaran_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_asuransi', function (Blueprint $table) {
            $table->dropColumn(['persetujuan', 'purchase_order_id']);
        });

        Schema::table('service_incidents', function (Blueprint $table) {
            $table->dropColumn(['persetujuan', 'keterangan', 'purchase_order_id']);
        });
    }
};
