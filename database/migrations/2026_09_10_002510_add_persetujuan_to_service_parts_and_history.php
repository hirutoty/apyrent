<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── service_parts ─────────────────────────────────────────────
        Schema::table('service_parts', function (Blueprint $table) {
            // Kolom persetujuan yang terlihat di UI (Pending/Disetujui/Ditolak)
            $table->enum('persetujuan', ['Pending', 'Disetujui', 'Ditolak'])
                  ->default('Pending')
                  ->after('status_approval');
        });

        // ── service_history ───────────────────────────────────────────
        Schema::table('service_history', function (Blueprint $table) {
            $table->enum('persetujuan', ['Pending', 'Disetujui', 'Ditolak'])
                  ->default('Pending')
                  ->after('status_approval');
        });

        // ── Seed kolom persetujuan dari status_approval existing ──────
        // service_parts
        DB::table('service_parts')->where('status_approval', 'approved')->update(['persetujuan' => 'Disetujui']);
        DB::table('service_parts')->where('status_approval', 'rejected')->update(['persetujuan' => 'Ditolak']);
        DB::table('service_parts')->where('status_approval', 'pending')->update(['persetujuan' => 'Pending']);
        // Record tanpa status_approval dianggap sudah disetujui (legacy data)
        DB::table('service_parts')->whereNull('status_approval')->update(['persetujuan' => 'Disetujui']);

        // service_history
        DB::table('service_history')->where('status_approval', 'approved')->update(['persetujuan' => 'Disetujui']);
        DB::table('service_history')->where('status_approval', 'rejected')->update(['persetujuan' => 'Ditolak']);
        DB::table('service_history')->where('status_approval', 'pending')->update(['persetujuan' => 'Pending']);
        DB::table('service_history')->whereNull('status_approval')->update(['persetujuan' => 'Disetujui']);
    }

    public function down(): void
    {
        Schema::table('service_parts', function (Blueprint $table) {
            $table->dropColumn('persetujuan');
        });

        Schema::table('service_history', function (Blueprint $table) {
            $table->dropColumn('persetujuan');
        });
    }
};
