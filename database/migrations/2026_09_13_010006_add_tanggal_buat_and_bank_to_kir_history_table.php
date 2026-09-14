<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kir_history', function (Blueprint $table) {
            $table->date('tanggal_buat')->nullable()->after('diperpanjang_pada');
            $table->string('nama_bank')->nullable()->after('tanggal_buat');
            $table->string('no_rekening')->nullable()->after('nama_bank');
        });
    }

    public function down(): void
    {
        Schema::table('kir_history', function (Blueprint $table) {
            $table->dropColumn(['tanggal_buat', 'nama_bank', 'no_rekening']);
        });
    }
};
