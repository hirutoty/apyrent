<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus data lama reminder_service (sistem baru fully otomatis dari part limit)
        DB::table('reminder_service')->truncate();

        Schema::table('reminder_service', function (Blueprint $table) {
            // FK ke service_parts — reminder sekarang selalu berkaitan dengan part
            $table->foreignId('service_part_id')
                ->nullable()
                ->after('kendaraan_id')
                ->constrained('service_parts')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('reminder_service', function (Blueprint $table) {
            $table->dropForeign(['service_part_id']);
            $table->dropColumn('service_part_id');
        });
    }
};
