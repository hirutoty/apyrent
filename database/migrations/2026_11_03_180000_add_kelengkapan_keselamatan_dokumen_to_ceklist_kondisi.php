<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ceklist_kondisi', function (Blueprint $table) {
            // Section B: Perlengkapan Keselamatan & Operasional (8 items)
            $table->enum('lampu_belakang_rem_mundur', ['B', 'R'])->nullable()->after('spedoometer');
            $table->enum('lampu_sein_hazard_posisi', ['B', 'R'])->nullable()->after('lampu_belakang_rem_mundur');
            $table->enum('wiper_washer', ['B', 'R'])->nullable()->after('lampu_sein_hazard_posisi');
            $table->enum('kaca_spion_kendaraan', ['B', 'R'])->nullable()->after('wiper_washer');
            $table->enum('sabuk_pengaman', ['B', 'R'])->nullable()->after('kaca_spion_kendaraan');
            $table->enum('ganjal_roda', ['B', 'R'])->nullable()->after('sabuk_pengaman');
            $table->enum('apar', ['B', 'R'])->nullable()->after('ganjal_roda');
            $table->enum('peralatan_darurat', ['B', 'R'])->nullable()->after('apar');
            
            // Section C: Kelengkapan Dokumen (4 items)
            $table->enum('kir_berlaku', ['B', 'R'])->nullable()->after('peralatan_darurat');
            $table->enum('sim_pengemudi', ['B', 'R'])->nullable()->after('kir_berlaku');
            $table->enum('dokumen_surat_jalan', ['B', 'R'])->nullable()->after('sim_pengemudi');
            $table->enum('kesesuaian_muatan', ['B', 'R'])->nullable()->after('dokumen_surat_jalan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ceklist_kondisi', function (Blueprint $table) {
            $table->dropColumn([
                'lampu_belakang_rem_mundur',
                'lampu_sein_hazard_posisi',
                'wiper_washer',
                'kaca_spion_kendaraan',
                'sabuk_pengaman',
                'ganjal_roda',
                'apar',
                'peralatan_darurat',
                'kir_berlaku',
                'sim_pengemudi',
                'dokumen_surat_jalan',
                'kesesuaian_muatan'
            ]);
        });
    }
};
