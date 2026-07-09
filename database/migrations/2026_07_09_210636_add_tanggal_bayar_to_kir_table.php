<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kir', function (Blueprint $table) {
            $table->date('tanggal_bayar')->nullable()->after('biaya');
        });
    }

    public function down(): void
    {
        Schema::table('kir', function (Blueprint $table) {
            $table->dropColumn('tanggal_bayar');
        });
    }
};
