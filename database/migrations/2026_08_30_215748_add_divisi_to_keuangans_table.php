<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('keuangans', function (Blueprint $table) {
            $table->string('divisi')->nullable()->after('user_id');
        });

        // Backfill data lama:
        // - Jika user ada → ambil role dan mapping ke label divisi
        // - Jika sumber = 'auto' atau user_id null → set "Keuangan"
        DB::statement("
            UPDATE keuangans k
            LEFT JOIN users u ON k.user_id = u.id
            SET k.divisi = CASE
                WHEN k.sumber = 'auto' OR k.user_id IS NULL THEN 'Keuangan'
                WHEN u.role = 'superadmin' THEN 'Superadmin'
                WHEN u.role = 'keuangan'   THEN 'Keuangan'
                WHEN u.role = 'produksi'   THEN 'Produksi'
                ELSE 'Keuangan'
            END
        ");
    }

    public function down(): void
    {
        Schema::table('keuangans', function (Blueprint $table) {
            $table->dropColumn('divisi');
        });
    }
};
