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
            // Section B: Pemeriksaan Khusus Truck
            
            // Sistem Rem & Air Brake (19-23)
            $table->enum('sistem_rem_angin', ['B', 'R'])->nullable()->after('kerapihanbody_cat');
            $table->enum('tekanan_angin_tabung', ['B', 'R'])->nullable()->after('sistem_rem_angin');
            $table->enum('kompresor_angin_saluran', ['B', 'R'])->nullable()->after('tekanan_angin_tabung');
            $table->enum('brake_chamber_slack_adjuster', ['B', 'R'])->nullable()->after('kompresor_angin_saluran');
            $table->enum('rem_parkir_tangan', ['B', 'R'])->nullable()->after('brake_chamber_slack_adjuster');
            
            // Transmisi & Gardan (24-26)
            $table->enum('transmisi_persneling', ['B', 'R'])->nullable()->after('rem_parkir_tangan');
            $table->enum('gardan_kebocoran_oli', ['B', 'R'])->nullable()->after('transmisi_persneling');
            $table->enum('kopel_propeller_universal', ['B', 'R'])->nullable()->after('gardan_kebocoran_oli');
            
            // Suspensi & Chassis (27-29)
            $table->enum('suspensi_per_daun', ['B', 'R'])->nullable()->after('kopel_propeller_universal');
            $table->enum('shock_absorber', ['B', 'R'])->nullable()->after('suspensi_per_daun');
            $table->enum('chassis_rangka_baut', ['B', 'R'])->nullable()->after('shock_absorber');
            
            // Ban & Roda (30-32)
            $table->enum('ban_depan_keausan', ['B', 'R'])->nullable()->after('chassis_rangka_baut');
            $table->enum('ban_belakang_ganda_keausan', ['B', 'R'])->nullable()->after('ban_depan_keausan');
            $table->enum('mur_baut_roda_velg', ['B', 'R'])->nullable()->after('ban_belakang_ganda_keausan');
            
            // Bak/Box & Muatan (33-36)
            $table->enum('bak_box_karoseri_lantai', ['B', 'R'])->nullable()->after('mur_baut_roda_velg');
            $table->enum('pintu_bak_sistem_pengunci', ['B', 'R'])->nullable()->after('bak_box_karoseri_lantai');
            $table->enum('engsel_pengait_pengaman', ['B', 'R'])->nullable()->after('pintu_bak_sistem_pengunci');
            $table->enum('terpal_tali_pengikat', ['B', 'R'])->nullable()->after('engsel_pengait_pengaman');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ceklist_kondisi', function (Blueprint $table) {
            $table->dropColumn([
                'sistem_rem_angin',
                'tekanan_angin_tabung',
                'kompresor_angin_saluran',
                'brake_chamber_slack_adjuster',
                'rem_parkir_tangan',
                'transmisi_persneling',
                'gardan_kebocoran_oli',
                'kopel_propeller_universal',
                'suspensi_per_daun',
                'shock_absorber',
                'chassis_rangka_baut',
                'ban_depan_keausan',
                'ban_belakang_ganda_keausan',
                'mur_baut_roda_velg',
                'bak_box_karoseri_lantai',
                'pintu_bak_sistem_pengunci',
                'engsel_pengait_pengaman',
                'terpal_tali_pengikat'
            ]);
        });
    }
};
