<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fix: semua PO dengan status 'Ditolak' yang belum memiliki can_edit = true
     * seharusnya bisa diajukan ulang oleh user.
     */
    public function up(): void
    {
        DB::table('purchase_orders')
            ->where('status', 'Ditolak')
            ->where('can_edit', false)
            ->update(['can_edit' => true]);
    }

    public function down(): void
    {
        // Tidak di-rollback karena tidak ada cara membedakan
        // mana yang sengaja can_edit=false vs yang kita perbaiki.
    }
};
