<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_detail', function (Blueprint $table) {
            $table->foreignId('service_history_id')
                ->nullable()
                ->after('kendaraan_id')
                ->constrained('service_history')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('service_detail', function (Blueprint $table) {
            $table->dropForeign(['service_history_id']);
            $table->dropColumn('service_history_id');
        });
    }
};
