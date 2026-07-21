<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kendaraan', function (Blueprint $table) {
            // Rename limit_bulan_service → limit_biaya_bulanan_service
            // dan ubah tipe dari integer ke bigInteger agar bisa tampung nilai rupiah besar
            $table->renameColumn('limit_bulan_service', 'limit_biaya_bulanan_service');
        });

        Schema::table('kendaraan', function (Blueprint $table) {
            // Ubah tipe jadi bigInteger
            $table->bigInteger('limit_biaya_bulanan_service')->default(0)->change();

            // Tambah kolom batas biaya tahunan
            $table->bigInteger('limit_biaya_tahunan_service')->default(0)->after('limit_biaya_bulanan_service');
        });
    }

    public function down(): void
    {
        Schema::table('kendaraan', function (Blueprint $table) {
            $table->dropColumn('limit_biaya_tahunan_service');
        });

        Schema::table('kendaraan', function (Blueprint $table) {
            $table->renameColumn('limit_biaya_bulanan_service', 'limit_bulan_service');
        });

        Schema::table('kendaraan', function (Blueprint $table) {
            $table->integer('limit_bulan_service')->default(0)->change();
        });
    }
};
