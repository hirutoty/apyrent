<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asuransi_kendaraan', function (Blueprint $table) {
            $table->string('nama_rekening')->nullable()->after('persetujuan');
            $table->string('nama_bank')->nullable()->after('nama_rekening');
            $table->string('no_rekening')->nullable()->after('nama_bank');
        });
    }

    public function down(): void
    {
        Schema::table('asuransi_kendaraan', function (Blueprint $table) {
            $table->dropColumn(['nama_rekening', 'nama_bank', 'no_rekening']);
        });
    }
};
