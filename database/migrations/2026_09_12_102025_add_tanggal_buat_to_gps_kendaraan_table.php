<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gps_kendaraan', function (Blueprint $table) {
            // Tanggal pertama kali GPS dipasang — tidak berubah saat perpanjang
            $table->date('tanggal_buat')->nullable()->after('tanggal_bayar');
        });

        // Backfill: isi tanggal_buat dari tanggal_pasang yang sudah ada
        DB::statement('UPDATE gps_kendaraan SET tanggal_buat = tanggal_pasang WHERE tanggal_buat IS NULL AND tanggal_pasang IS NOT NULL');
    }

    public function down(): void
    {
        Schema::table('gps_kendaraan', function (Blueprint $table) {
            $table->dropColumn('tanggal_buat');
        });
    }
};
