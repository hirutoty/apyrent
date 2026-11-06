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
            // Hapus kolom ban keausan
            $table->dropColumn([
                'ban_depan_keausan',
                'ban_belakang_ganda_keausan'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ceklist_kondisi', function (Blueprint $table) {
            // Restore kolom jika rollback
            $table->enum('ban_depan_keausan', ['B', 'R'])->nullable();
            $table->enum('ban_belakang_ganda_keausan', ['B', 'R'])->nullable();
        });
    }
};
