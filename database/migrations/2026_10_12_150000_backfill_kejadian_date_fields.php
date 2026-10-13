<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Backfill date fields untuk existing service_asuransi_kejadians records.
     * 
     * Copy tanggal_service, periode_mulai, periode_selesai dari parent service_asuransi.
     * Untuk tanggal_approval: gunakan created_at jika kejadian status = 'disetujui'.
     */
    public function up(): void
    {
        // Update kejadians dengan data dari parent service_asuransi
        DB::statement("
            UPDATE service_asuransi_kejadians AS k
            INNER JOIN service_asuransi AS sa ON k.service_asuransi_id = sa.id
            SET 
                k.tanggal = COALESCE(k.tanggal, sa.tanggal_service),
                k.periode_mulai = COALESCE(k.periode_mulai, sa.periode_mulai),
                k.periode_selesai = COALESCE(k.periode_selesai, sa.periode_selesai),
                k.tanggal_approval = CASE
                    WHEN k.status = 'disetujui' AND k.tanggal_approval IS NULL 
                    THEN COALESCE(k.updated_at, k.created_at, sa.created_at)
                    ELSE k.tanggal_approval
                END
            WHERE k.tanggal IS NULL 
               OR k.periode_mulai IS NULL 
               OR k.periode_selesai IS NULL
               OR (k.status = 'disetujui' AND k.tanggal_approval IS NULL)
        ");

        // Log hasil backfill
        $updatedCount = DB::table('service_asuransi_kejadians')
            ->whereNotNull('tanggal')
            ->count();
        
        \Log::info("Backfill kejadian date fields completed: {$updatedCount} records updated");
    }

    public function down(): void
    {
        // Rollback: set kembali ke NULL jika diperlukan
        // PERHATIAN: Ini akan menghapus semua date data, termasuk yang baru
        // Uncomment hanya jika benar-benar diperlukan untuk testing
        
        // DB::table('service_asuransi_kejadians')->update([
        //     'tanggal' => null,
        //     'periode_mulai' => null,
        //     'periode_selesai' => null,
        //     'tanggal_approval' => null,
        // ]);
    }
};
