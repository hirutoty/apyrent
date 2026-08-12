<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_leasings', function (Blueprint $table) {
            // Jumlah cicilan manual — nullable, jika diisi mengesampingkan hitungan otomatis dari periode
            $table->unsignedSmallInteger('jumlah_cicilan')->nullable()->after('periode_selesai');
        });
    }

    public function down(): void
    {
        Schema::table('data_leasings', function (Blueprint $table) {
            $table->dropColumn('jumlah_cicilan');
        });
    }
};
