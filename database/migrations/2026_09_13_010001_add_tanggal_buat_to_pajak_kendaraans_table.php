<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pajak_kendaraans', function (Blueprint $table) {
            $table->date('tanggal_buat')->nullable()->after('tanggal_bayar');
        });

        // Backfill: isi dari tanggal_bayar yang sudah ada
        DB::statement('UPDATE pajak_kendaraans SET tanggal_buat = tanggal_bayar WHERE tanggal_buat IS NULL AND tanggal_bayar IS NOT NULL');
    }

    public function down(): void
    {
        Schema::table('pajak_kendaraans', function (Blueprint $table) {
            $table->dropColumn('tanggal_buat');
        });
    }
};
