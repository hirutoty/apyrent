<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * MEDIUM #4: Ubah kolom total di aging_ars dari bigInteger ke decimal(15,2).
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE aging_ars MODIFY total DECIMAL(15,2) DEFAULT 0');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE aging_ars MODIFY total BIGINT DEFAULT 0');
    }
};
