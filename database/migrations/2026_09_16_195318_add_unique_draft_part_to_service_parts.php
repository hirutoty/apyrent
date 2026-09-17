<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Unique index: satu part per (service_history_id + nama_part + posisi + category_id).
     * Mencegah duplikasi parts saat createServiceHistoryDraft dipanggil ganda.
     * Menggunakan partial index workaround karena MySQL tidak support partial unique index —
     * pakai kombinasi kolom dengan COALESCE via generated column tidak tersedia langsung.
     * Solusi: composite unique pada (service_history_id, nama_part, posisi, category_id).
     * Posisi dan category_id bisa NULL — MySQL treats NULLs as distinct di unique index,
     * jadi kita TIDAK bisa pakai unique index langsung untuk nullable cols.
     * Sebagai gantinya, guard duplikat dilakukan di aplikasi (lockForUpdate) + migration ini
     * sebatas dokumentasi intent.
     *
     * UPDATE: Skip DB unique — cukup dengan aplikasi-level guard + unique pada service_history.pembayaran_id.
     */
    public function up(): void
    {
        // Tidak tambah unique constraint di service_parts karena nullable cols (posisi, category_id)
        // akan meloloskan duplikat di MySQL unique index.
        // Guard duplikat sudah cukup di aplikasi (lockForUpdate + SH unique pembayaran_id).
    }

    public function down(): void
    {
        //
    }
};
