<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop supplier_id dari service_incidents
        Schema::table('service_incidents', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropColumn('supplier_id');
        });

        // 2. Tambah supplier_id ke service_incident_parts (nullable)
        Schema::table('service_incident_parts', function (Blueprint $table) {
            $table->foreignId('supplier_id')
                ->nullable()
                ->after('keterangan')
                ->constrained('supplier')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        // Rollback: hapus supplier_id dari parts, kembalikan ke incidents
        Schema::table('service_incident_parts', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropColumn('supplier_id');
        });

        Schema::table('service_incidents', function (Blueprint $table) {
            $table->foreignId('supplier_id')
                ->nullable()
                ->constrained('supplier')
                ->nullOnDelete();
        });
    }
};
