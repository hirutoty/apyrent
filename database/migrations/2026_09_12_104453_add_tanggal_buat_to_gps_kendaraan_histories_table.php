<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gps_kendaraan_histories', function (Blueprint $table) {
            $table->date('tanggal_buat')->nullable()->after('tanggal_bayar');
        });

        // Backfill dari parent gps_kendaraan.tanggal_buat
        DB::statement('
            UPDATE gps_kendaraan_histories h
            JOIN gps_kendaraan g ON g.id = h.gps_kendaraan_id
            SET h.tanggal_buat = g.tanggal_buat
            WHERE h.tanggal_buat IS NULL AND g.tanggal_buat IS NOT NULL
        ');
    }

    public function down(): void
    {
        Schema::table('gps_kendaraan_histories', function (Blueprint $table) {
            $table->dropColumn('tanggal_buat');
        });
    }
};
