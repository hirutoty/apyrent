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
            // Tambah kolom ban ganda setelah ban_belakang_kiri
            $table->enum('ban_ganda_depan_kanan', ['B', 'R'])->nullable()->after('ban_belakang_kiri');
            $table->enum('ban_ganda_depan_kiri', ['B', 'R'])->nullable()->after('ban_ganda_depan_kanan');
            $table->enum('ban_ganda_belakang_kanan', ['B', 'R'])->nullable()->after('ban_ganda_depan_kiri');
            $table->enum('ban_ganda_belakang_kiri', ['B', 'R'])->nullable()->after('ban_ganda_belakang_kanan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ceklist_kondisi', function (Blueprint $table) {
            $table->dropColumn([
                'ban_ganda_depan_kanan',
                'ban_ganda_depan_kiri',
                'ban_ganda_belakang_kanan',
                'ban_ganda_belakang_kiri'
            ]);
        });
    }
};
