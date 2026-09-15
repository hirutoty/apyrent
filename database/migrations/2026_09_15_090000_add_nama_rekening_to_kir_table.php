<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kir', function (Blueprint $table) {
            $table->string('nama_rekening')->nullable()->after('status');
            $table->string('nama_bank')->nullable()->after('nama_rekening');
            $table->string('no_rekening', 100)->nullable()->after('nama_bank');
        });
    }

    public function down(): void
    {
        Schema::table('kir', function (Blueprint $table) {
            $table->dropColumn(['nama_rekening', 'nama_bank', 'no_rekening']);
        });
    }
};
