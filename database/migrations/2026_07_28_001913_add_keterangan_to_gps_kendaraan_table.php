<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gps_kendaraan', function (Blueprint $table) {
            $table->text('keterangan')->nullable()->after('bukti_bayar');
        });
    }

    public function down(): void
    {
        Schema::table('gps_kendaraan', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
    }
};
