<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * MEDIUM #4 / P1 #2: Ubah kolom debit, kredit, saldo di bukubesars dari bigInteger ke decimal(15,2).
     * MySQL-compatible via MODIFY; SQLite tidak mendukung MODIFY — kolom dibuat ulang via Schema.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            // SQLite: recreate table with new column types
            Schema::table('bukubesars', function (Blueprint $table) {
                $table->decimal('debit',  15, 2)->default(0)->change();
                $table->decimal('kredit', 15, 2)->default(0)->change();
                $table->decimal('saldo',  15, 2)->default(0)->change();
            });
            return;
        }

        DB::statement('ALTER TABLE bukubesars MODIFY debit DECIMAL(15,2) DEFAULT 0');
        DB::statement('ALTER TABLE bukubesars MODIFY kredit DECIMAL(15,2) DEFAULT 0');
        DB::statement('ALTER TABLE bukubesars MODIFY saldo DECIMAL(15,2) DEFAULT 0');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            Schema::table('bukubesars', function (Blueprint $table) {
                $table->bigInteger('debit')->default(0)->change();
                $table->bigInteger('kredit')->default(0)->change();
                $table->bigInteger('saldo')->default(0)->change();
            });
            return;
        }

        DB::statement('ALTER TABLE bukubesars MODIFY debit BIGINT DEFAULT 0');
        DB::statement('ALTER TABLE bukubesars MODIFY kredit BIGINT DEFAULT 0');
        DB::statement('ALTER TABLE bukubesars MODIFY saldo BIGINT DEFAULT 0');
    }
};
