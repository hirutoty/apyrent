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
            // Drop 5 old lampu fields
            $table->dropColumn([
                'lampu_lampusen',
                'lampu_lampubesarkecil',
                'lampu_rembikplat',
                'lampu_belakang_rem_mundur',
                'lampu_sein_hazard_posisi',
            ]);
            
            // Add 1 parent + 8 child lampu fields (after kondisi_ac)
            $table->string('kondisi_lampu', 1)->nullable()->after('kondisi_ac'); // Parent (B/R)
            $table->string('lampu_sein', 1)->nullable()->after('kondisi_lampu');
            $table->string('lampu_bik_plat', 1)->nullable()->after('lampu_sein');
            $table->string('lampu_hazard', 1)->nullable()->after('lampu_bik_plat');
            $table->string('lampu_kota', 1)->nullable()->after('lampu_hazard');
            $table->string('lampu_besar_kecil', 1)->nullable()->after('lampu_kota');
            $table->string('lampu_mundur', 1)->nullable()->after('lampu_besar_kecil');
            $table->string('lampu_rem_belakang', 1)->nullable()->after('lampu_mundur');
            $table->string('lampu_rem_depan', 1)->nullable()->after('lampu_rem_belakang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ceklist_kondisi', function (Blueprint $table) {
            // Drop 9 new lampu fields
            $table->dropColumn([
                'kondisi_lampu',
                'lampu_sein',
                'lampu_bik_plat',
                'lampu_hazard',
                'lampu_kota',
                'lampu_besar_kecil',
                'lampu_mundur',
                'lampu_rem_belakang',
                'lampu_rem_depan',
            ]);
            
            // Restore 5 old lampu fields
            $table->string('lampu_lampusen', 1)->nullable()->after('kondisi_ac');
            $table->string('lampu_lampubesarkecil', 1)->nullable()->after('lampu_lampusen');
            $table->string('lampu_rembikplat', 1)->nullable()->after('lampu_lampubesarkecil');
            $table->string('lampu_belakang_rem_mundur', 1)->nullable()->after('terpal_tali_pengikat');
            $table->string('lampu_sein_hazard_posisi', 1)->nullable()->after('lampu_belakang_rem_mundur');
        });
    }
};
