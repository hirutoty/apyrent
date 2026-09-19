<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_category_limits', function (Blueprint $table) {
            // limit_km_interval: selisih KM tetap per penggantian (misal: 20.000 km)
            // Nilai ini tidak berubah — digunakan untuk menghitung target berikutnya
            $table->bigInteger('limit_km_interval')
                ->nullable()
                ->after('limit_km')
                ->comment('Interval KM tetap per penggantian part. limit_km akan diupdate = limit_km + limit_km_interval setiap kali pembayaran diapprove.');
        });

        // Isi limit_km_interval dari nilai limit_km yang sudah ada
        // (asumsi: nilai limit_km lama = interval, bukan target absolut)
        DB::statement('UPDATE service_category_limits SET limit_km_interval = limit_km WHERE limit_km IS NOT NULL AND limit_km > 0');
    }

    public function down(): void
    {
        Schema::table('service_category_limits', function (Blueprint $table) {
            $table->dropColumn('limit_km_interval');
        });
    }
};
