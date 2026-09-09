<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pajak_kendaraans', function (Blueprint $table) {
            $table->string('nama_pemilik')->nullable()->after('keterangan');
            $table->string('nama_bank')->nullable()->after('nama_pemilik');
            $table->string('no_rekening')->nullable()->after('nama_bank');
        });
    }

    public function down(): void
    {
        Schema::table('pajak_kendaraans', function (Blueprint $table) {
            $table->dropColumn(['nama_pemilik', 'nama_bank', 'no_rekening']);
        });
    }
};
