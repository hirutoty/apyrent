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
            $table->dropColumn(['peralatan_darurat', 'kunciroda_truck']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ceklist_kondisi', function (Blueprint $table) {
            $table->string('peralatan_darurat')->nullable();
            $table->string('kunciroda_truck')->nullable();
        });
    }
};
