<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if wrong foreign key exists, drop it
        try {
            Schema::table('service_incidents', function (Blueprint $table) {
                $table->dropForeign(['pembayaran_id']);
            });
        } catch (\Exception $e) {
            // Foreign key might already be dropped or doesn't exist, continue
        }

        // Add correct foreign key
        Schema::table('service_incidents', function (Blueprint $table) {
            $table->foreign('pembayaran_id')
                ->references('id')
                ->on('pembayarans')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('service_incidents', function (Blueprint $table) {
            $table->dropForeign(['pembayaran_id']);
        });

        Schema::table('service_incidents', function (Blueprint $table) {
            // Restore foreign key ke purchase_orders (rollback to original wrong state)
            $table->foreign('pembayaran_id')
                ->references('id')
                ->on('purchase_orders')
                ->nullOnDelete();
        });
    }
};
