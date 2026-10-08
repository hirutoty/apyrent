<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah enum persetujuan di service_incident_parts agar mencakup semua nilai
        // yang dipakai di alur pembayaran: 'Diajukan ke Pembayaran' dan 'Ditolak Pembayaran'
        DB::statement("
            ALTER TABLE service_incident_parts
            MODIFY COLUMN persetujuan
                ENUM('Pending','Disetujui','Ditolak','Diajukan ke Pembayaran','Ditolak Pembayaran')
                NOT NULL DEFAULT 'Pending'
        ");
    }

    public function down(): void
    {
        // Kembalikan ke enum semula (data yang memakai nilai baru akan hilang)
        DB::statement("
            ALTER TABLE service_incident_parts
            MODIFY COLUMN persetujuan
                ENUM('Pending','Disetujui','Ditolak')
                NOT NULL DEFAULT 'Pending'
        ");
    }
};
