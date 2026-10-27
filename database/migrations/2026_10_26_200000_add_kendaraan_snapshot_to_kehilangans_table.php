<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kehilangans', function (Blueprint $table) {
            // Snapshot data kendaraan untuk histori
            $table->string('kendaraan_nopol')->nullable()->after('kendaraan_id');
            $table->string('kendaraan_merk')->nullable()->after('kendaraan_nopol');
            $table->string('kendaraan_type')->nullable()->after('kendaraan_merk');
            $table->string('kendaraan_warna')->nullable()->after('kendaraan_type');
            $table->string('kendaraan_tahun')->nullable()->after('kendaraan_warna');
        });
    }

    public function down(): void
    {
        Schema::table('kehilangans', function (Blueprint $table) {
            $table->dropColumn([
                'kendaraan_nopol',
                'kendaraan_merk',
                'kendaraan_type',
                'kendaraan_warna',
                'kendaraan_tahun'
            ]);
        });
    }
};
