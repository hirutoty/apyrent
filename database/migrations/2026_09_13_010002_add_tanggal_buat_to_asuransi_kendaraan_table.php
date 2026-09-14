<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asuransi_kendaraan', function (Blueprint $table) {
            $table->date('tanggal_buat')->nullable()->after('tanggal_bayar');
        });

        // Backfill: isi dari tgl_mulai yang sudah ada
        DB::statement('UPDATE asuransi_kendaraan SET tanggal_buat = tgl_mulai WHERE tanggal_buat IS NULL AND tgl_mulai IS NOT NULL');
    }

    public function down(): void
    {
        Schema::table('asuransi_kendaraan', function (Blueprint $table) {
            $table->dropColumn('tanggal_buat');
        });
    }
};
