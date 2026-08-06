<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kendaraan', function (Blueprint $table) {
            $table->text('foto_masalah')->nullable()->after('foto'); // JSON array path foto
            $table->text('catatan_masalah')->nullable()->after('foto_masalah');
        });
    }

    public function down(): void
    {
        Schema::table('kendaraan', function (Blueprint $table) {
            $table->dropColumn(['foto_masalah', 'catatan_masalah']);
        });
    }
};
