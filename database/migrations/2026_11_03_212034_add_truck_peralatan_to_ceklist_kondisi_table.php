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
        Schema::table('ceklist_kondisi', function (Blueprint $table) {
            // Tambah kolom untuk peralatan truck setelah terpal_tali_pengikat
            $table->enum('kunciroda_truck', ['B', 'R'])->nullable()->after('terpal_tali_pengikat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ceklist_kondisi', function (Blueprint $table) {
            $table->dropColumn('kunciroda_truck');
        });
    }
};
