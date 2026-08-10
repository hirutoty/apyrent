<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inv_penawarans', function (Blueprint $table) {
            // Default 'bulan' agar data lama tetap valid
            $table->string('periode_satuan', 10)->default('bulan')->after('periode');
        });
    }

    public function down(): void
    {
        Schema::table('inv_penawarans', function (Blueprint $table) {
            $table->dropColumn('periode_satuan');
        });
    }
};
