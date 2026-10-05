<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Refactor approval flow:
     * - Record GPS, ServiceIncident, ServiceAsuransi tidak lagi dibuat saat store()
     * - Record baru dibuat saat PO disetujui dengan persetujuan = 'Diajukan ke Pembayaran'
     * - Saat Pembayaran disetujui → 'Disetujui'
     * - Saat Pembayaran ditolak → 'Ditolak Pembayaran' (nilai baru)
     *
     * Migration ini:
     * 1. Tambah 'Ditolak Pembayaran' ke ENUM gps_kendaraan.persetujuan
     * 2. Hapus record gps_kendaraan yang masih Pending (belum punya pembayaran → orphan)
     * 3. Hapus record service_asuransi yang masih Pending (orphan)
     * 4. Hapus record service_incidents yang masih Pending (orphan)
     */
    public function up(): void
    {
        // ── 1. Update ENUM gps_kendaraan.persetujuan ──────────────────────────
        // Tambah 'Ditolak Pembayaran' sebagai nilai baru
        DB::statement("ALTER TABLE gps_kendaraan MODIFY COLUMN persetujuan ENUM(
            'Pending',
            'Diajukan ke Pembayaran',
            'Disetujui',
            'Ditolak',
            'Ditolak Pembayaran'
        ) NULL DEFAULT NULL");

        // ── 2. Hapus GPS records Pending yang tidak punya pembayaran_id ──────
        // Ini adalah record "orphan" yang dibuat saat store() dengan alur lama.
        // Record yang Ditolak tapi punya pembayaran_id (ditolak di Pembayaran) tetap dipertahankan.
        DB::table('gps_kendaraan')
            ->where('persetujuan', 'Pending')
            ->whereNull('pembayaran_id')
            ->delete();

        // Juga hapus attachment orphan yang relation_id-nya sudah tidak ada
        $remainingGpsIds = DB::table('gps_kendaraan')->pluck('id');
        DB::table('attachments')
            ->where('relation_type', 'gps')
            ->whereNotIn('relation_id', $remainingGpsIds)
            ->delete();

        // ── 3. Hapus service_asuransi records Pending ─────────────────────────
        // Hapus dulu kejadian terkait, lalu record induknya
        $pendingSaIds = DB::table('service_asuransi')
            ->where('persetujuan', 'Pending')
            ->whereNull('pembayaran_id')
            ->pluck('id');

        if ($pendingSaIds->isNotEmpty()) {
            DB::table('service_asuransi_kejadians')
                ->whereIn('service_asuransi_id', $pendingSaIds)
                ->delete();

            DB::table('service_asuransi')
                ->whereIn('id', $pendingSaIds)
                ->delete();
        }

        // ── 4. Hapus service_incidents records Pending ────────────────────────
        $pendingSiIds = DB::table('service_incidents')
            ->where('persetujuan', 'Pending')
            ->whereNull('pembayaran_id')
            ->pluck('id');

        if ($pendingSiIds->isNotEmpty()) {
            DB::table('service_incident_parts')
                ->whereIn('service_incident_id', $pendingSiIds)
                ->delete();

            DB::table('service_incidents')
                ->whereIn('id', $pendingSiIds)
                ->delete();
        }
    }

    public function down(): void
    {
        // Kembalikan ENUM ke nilai sebelumnya (tanpa Ditolak Pembayaran)
        DB::statement("ALTER TABLE gps_kendaraan MODIFY COLUMN persetujuan ENUM(
            'Pending',
            'Diajukan ke Pembayaran',
            'Disetujui',
            'Ditolak'
        ) NULL DEFAULT NULL");

        // Data yang dihapus tidak bisa dikembalikan (irreversible cleanup)
    }
};
