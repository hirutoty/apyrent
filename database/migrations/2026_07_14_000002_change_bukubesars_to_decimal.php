<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * MEDIUM #4 / P1 #2: Ubah kolom debit, kredit, saldo di bukubesars dari bigInteger ke decimal(15,2).
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE bukubesars MODIFY debit DECIMAL(15,2) DEFAULT 0');
        DB::statement('ALTER TABLE bukubesars MODIFY kredit DECIMAL(15,2) DEFAULT 0');
        DB::statement('ALTER TABLE bukubesars MODIFY saldo DECIMAL(15,2) DEFAULT 0');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE bukubesars MODIFY debit BIGINT DEFAULT 0');
        DB::statement('ALTER TABLE bukubesars MODIFY kredit BIGINT DEFAULT 0');
        DB::statement('ALTER TABLE bukubesars MODIFY saldo BIGINT DEFAULT 0');
    }
};
