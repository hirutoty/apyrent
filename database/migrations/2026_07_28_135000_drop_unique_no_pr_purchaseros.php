<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cek keberadaan index via information_schema, lalu drop kalau ada
        $indexExists = DB::selectOne("
            SELECT COUNT(*) as cnt
            FROM information_schema.statistics
            WHERE table_schema = DATABASE()
              AND table_name   = 'purchaseros'
              AND index_name   = 'purchaseros_no_pr_unique'
        ");

        if ($indexExists && $indexExists->cnt > 0) {
            Schema::table('purchaseros', function (Blueprint $table) {
                $table->dropUnique('purchaseros_no_pr_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::table('purchaseros', function (Blueprint $table) {
            $table->unique('no_pr');
        });
    }
};
