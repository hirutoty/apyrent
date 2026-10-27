<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Backfill snapshot data untuk record yang sudah ada
        DB::statement("
            UPDATE kehilangans k
            INNER JOIN kendaraan kend ON k.kendaraan_id = kend.id
            SET 
                k.kendaraan_nopol = kend.nopol,
                k.kendaraan_merk = kend.merk,
                k.kendaraan_warna = kend.warna,
                k.kendaraan_tahun = kend.tahun_pembuatan
            WHERE k.kendaraan_nopol IS NULL
        ");
    }

    public function down(): void
    {
        // Tidak perlu rollback untuk data backfill
    }
};
