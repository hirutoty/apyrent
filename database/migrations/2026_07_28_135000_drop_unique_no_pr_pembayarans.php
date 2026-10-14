<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            // SQLite: cek index via PRAGMA
            $indices = DB::select("PRAGMA index_list('pembayarans')");
            $exists  = collect($indices)->contains(fn($i) => ($i->name ?? '') === 'pembayarans_no_pr_unique');
            if ($exists) {
                Schema::table('pembayarans', function (Blueprint $table) {
                    $table->dropUnique('pembayarans_no_pr_unique');
                });
            }
            return;
        }

        // MySQL: cek via information_schema
        $indexExists = DB::selectOne("
            SELECT COUNT(*) as cnt
            FROM information_schema.statistics
            WHERE table_schema = DATABASE()
              AND table_name   = 'pembayarans'
              AND index_name   = 'pembayarans_no_pr_unique'
        ");

        if ($indexExists && $indexExists->cnt > 0) {
            Schema::table('pembayarans', function (Blueprint $table) {
                $table->dropUnique('pembayarans_no_pr_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->unique('no_pr');
        });
    }
};
