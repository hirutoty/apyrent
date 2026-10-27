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
            $table->dropColumn(['foto_dalam', 'ket_dalam']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ceklist_kondisi', function (Blueprint $table) {
            $table->string('foto_dalam')->nullable()->after('foto_atas');
            $table->text('ket_dalam')->nullable()->after('ket_atas');
        });
    }
};
