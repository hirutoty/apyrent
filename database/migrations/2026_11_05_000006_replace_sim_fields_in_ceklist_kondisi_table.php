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
            // Drop old SIM fields
            $table->dropColumn(['sim_b', 'sim_c', 'sim_d', 'sim_internasional']);
            
            // Add new SIM fields after sim_a
            $table->string('sim_b1')->nullable()->after('sim_a');
            $table->string('sim_b1_umum')->nullable()->after('sim_b1');
            $table->string('sim_b2')->nullable()->after('sim_b1_umum');
            $table->string('sim_b2_umum')->nullable()->after('sim_b2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ceklist_kondisi', function (Blueprint $table) {
            // Drop new SIM fields
            $table->dropColumn(['sim_b1', 'sim_b1_umum', 'sim_b2', 'sim_b2_umum']);
            
            // Re-add old SIM fields
            $table->string('sim_b')->nullable()->after('sim_a');
            $table->string('sim_c')->nullable()->after('sim_b');
            $table->string('sim_d')->nullable()->after('sim_c');
            $table->string('sim_internasional')->nullable()->after('sim_d');
        });
    }
};
