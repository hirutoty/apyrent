<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gps_kendaraan', function (Blueprint $table) {
            $table->string('nama_bank', 255)->nullable()->after('keterangan');
            $table->string('no_rekening', 100)->nullable()->after('nama_bank');
            $table->string('nama_pemilik', 255)->nullable()->after('no_rekening');
        });
    }

    public function down(): void
    {
        Schema::table('gps_kendaraan', function (Blueprint $table) {
            $table->dropColumn(['nama_bank', 'no_rekening', 'nama_pemilik']);
        });
    }
};
