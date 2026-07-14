<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * MEDIUM #11: Tambah kolom 'sumber' ENUM('manual','auto') default 'manual' ke tabel keuangans.
     */
    public function up(): void
    {
        Schema::table('keuangans', function (Blueprint $table) {
            $table->enum('sumber', ['manual', 'auto'])->default('manual')->after('saldo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('keuangans', function (Blueprint $table) {
            $table->dropColumn('sumber');
        });
    }
};
