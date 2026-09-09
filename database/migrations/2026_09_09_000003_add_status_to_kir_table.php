<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kir', function (Blueprint $table) {
            $table->string('status', 20)->default('aktif')->after('persetujuan');
        });

        // Record yang sudah ada (masa_berlaku sudah lewat) → habis
        DB::statement("UPDATE kir SET status = 'habis' WHERE masa_berlaku < CURDATE()");
        // Record pending (belum diapprove) → tidak_aktif
        DB::statement("UPDATE kir SET status = 'tidak_aktif' WHERE persetujuan = 'Pending' OR persetujuan IS NULL");
        // Sisanya tetap aktif
    }

    public function down(): void
    {
        Schema::table('kir', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
