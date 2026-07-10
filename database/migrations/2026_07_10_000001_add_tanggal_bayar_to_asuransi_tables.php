<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asuransi_kendaraan', function (Blueprint $table) {
            if (!Schema::hasColumn('asuransi_kendaraan', 'tanggal_bayar')) {
                $table->date('tanggal_bayar')->nullable()->after('biaya');
            }
        });

        Schema::table('asuransi_history', function (Blueprint $table) {
            if (!Schema::hasColumn('asuransi_history', 'tanggal_bayar')) {
                $table->date('tanggal_bayar')->nullable()->after('biaya');
            }
        });
    }

    public function down(): void
    {
        Schema::table('asuransi_kendaraan', function (Blueprint $table) {
            if (Schema::hasColumn('asuransi_kendaraan', 'tanggal_bayar')) {
                $table->dropColumn('tanggal_bayar');
            }
        });

        Schema::table('asuransi_history', function (Blueprint $table) {
            if (Schema::hasColumn('asuransi_history', 'tanggal_bayar')) {
                $table->dropColumn('tanggal_bayar');
            }
        });
    }
};
