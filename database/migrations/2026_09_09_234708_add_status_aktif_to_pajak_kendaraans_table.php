<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pajak_kendaraans', function (Blueprint $table) {
            $table->enum('status_aktif', ['tidak_aktif', 'aktif', 'expired'])
                  ->default('tidak_aktif')
                  ->after('status');
        });

        // Seed: record yang sudah Disetujui & jatuh_tempo >= hari ini → aktif
        DB::statement("
            UPDATE pajak_kendaraans
            SET status_aktif = 'aktif'
            WHERE persetujuan = 'Disetujui'
              AND jatuh_tempo >= CURDATE()
        ");

        // Seed: record yang sudah Disetujui & jatuh_tempo < hari ini → expired
        DB::statement("
            UPDATE pajak_kendaraans
            SET status_aktif = 'expired'
            WHERE persetujuan = 'Disetujui'
              AND jatuh_tempo < CURDATE()
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pajak_kendaraans', function (Blueprint $table) {
            $table->dropColumn('status_aktif');
        });
    }
};
