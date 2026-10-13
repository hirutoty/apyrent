<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Backfill tanggal_approval untuk existing service_incident_parts records.
     * 
     * Untuk parts dengan persetujuan='Disetujui', gunakan updated_at sebagai fallback.
     */
    public function up(): void
    {
        DB::statement("
            UPDATE service_incident_parts
            SET tanggal_approval = COALESCE(
                updated_at,
                created_at
            )
            WHERE persetujuan = 'Disetujui'
              AND tanggal_approval IS NULL
        ");

        // Log hasil backfill
        $updatedCount = DB::table('service_incident_parts')
            ->where('persetujuan', 'Disetujui')
            ->whereNotNull('tanggal_approval')
            ->count();
        
        \Log::info("Backfill tanggal_approval for service_incident_parts completed: {$updatedCount} records updated");
    }

    public function down(): void
    {
        // Rollback: set tanggal_approval kembali ke NULL untuk records yang di-backfill
        // PERHATIAN: Ini akan menghapus semua tanggal_approval, termasuk yang baru
        // Uncomment hanya jika benar-benar diperlukan untuk testing
        
        // DB::table('service_incident_parts')
        //     ->where('persetujuan', 'Disetujui')
        //     ->update(['tanggal_approval' => null]);
    }
};
