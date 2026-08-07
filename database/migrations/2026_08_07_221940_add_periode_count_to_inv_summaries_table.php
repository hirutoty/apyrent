<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inv_summaries', function (Blueprint $table) {
            // Jumlah periode yang dicakup invoice ini (default 1)
            $table->unsignedTinyInteger('periode_count')->default(1)->after('invoice_id');
        });
    }

    public function down(): void
    {
        Schema::table('inv_summaries', function (Blueprint $table) {
            $table->dropColumn('periode_count');
        });
    }
};
