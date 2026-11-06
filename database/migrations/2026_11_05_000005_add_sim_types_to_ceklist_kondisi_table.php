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
            $table->string('sim_a')->nullable()->after('sim_pengemudi');
            $table->string('sim_b')->nullable()->after('sim_a');
            $table->string('sim_c')->nullable()->after('sim_b');
            $table->string('sim_d')->nullable()->after('sim_c');
            $table->string('sim_internasional')->nullable()->after('sim_d');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ceklist_kondisi', function (Blueprint $table) {
            $table->dropColumn(['sim_a', 'sim_b', 'sim_c', 'sim_d', 'sim_internasional']);
        });
    }
};
