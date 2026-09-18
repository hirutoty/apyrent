<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tambah nilai 'bukti_global' ke enum action di pembayaran_approvals.
     * Dipakai saat menyimpan bukti pembayaran global untuk service_incident.
     */
    public function up(): void
    {
        // MySQL ALTER TABLE untuk menambah nilai ke ENUM
        DB::statement("ALTER TABLE `pembayaran_approvals`
            MODIFY COLUMN `action` ENUM('approved', 'rejected', 'bukti_global', 'submitted', 'approved_partial')
            COMMENT 'Approval action'");
    }

    public function down(): void
    {
        // Kembalikan ke nilai semula (tanpa bukti_global, submitted, approved_partial)
        DB::statement("ALTER TABLE `pembayaran_approvals`
            MODIFY COLUMN `action` ENUM('approved', 'rejected')
            COMMENT 'Approval action: approved or rejected'");
    }
};
