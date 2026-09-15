<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_incidents', function (Blueprint $table) {
            // Referensi ke PurchaseOrder yang menghasilkan record ini
            $table->unsignedBigInteger('pembayaran_id')
                ->nullable()
                ->after('approval_at');

            $table->foreign('pembayaran_id')
                ->references('id')
                ->on('purchase_orders')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('service_incidents', function (Blueprint $table) {
            $table->dropForeign(['pembayaran_id']);
            $table->dropColumn('pembayaran_id');
        });
    }
};
