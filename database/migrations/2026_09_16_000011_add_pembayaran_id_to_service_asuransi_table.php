<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_asuransi', function (Blueprint $table) {
            $table->foreignId('pembayaran_id')
                  ->nullable()
                  ->after('status')
                  ->constrained('pembayarans')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('service_asuransi', function (Blueprint $table) {
            $table->dropForeign(['pembayaran_id']);
            $table->dropColumn('pembayaran_id');
        });
    }
};
