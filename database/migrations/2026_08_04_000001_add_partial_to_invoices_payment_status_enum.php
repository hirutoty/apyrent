<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah ENUM payment_status di tabel invoices untuk menambahkan 'partial'
        DB::statement("ALTER TABLE invoices MODIFY COLUMN payment_status ENUM('unpaid', 'paid', 'partial') DEFAULT 'unpaid'");
    }

    public function down(): void
    {
        // Revert — data dengan nilai 'partial' akan jadi '' (truncated) saat rollback
        DB::statement("ALTER TABLE invoices MODIFY COLUMN payment_status ENUM('unpaid', 'paid') DEFAULT 'unpaid'");
    }
};
