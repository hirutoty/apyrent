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
        Schema::table('bukubesars', function (Blueprint $table) {
            // Kolom referensi untuk melacak sumber jurnal otomatis (misal: no_pr dari Purchasero)
            $table->string('referensi')->nullable()->after('keterangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bukubesars', function (Blueprint $table) {
            $table->dropColumn('referensi');
        });
    }
};
