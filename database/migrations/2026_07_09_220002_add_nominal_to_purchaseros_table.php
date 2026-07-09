<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchaseros', function (Blueprint $table) {
            $table->bigInteger('nominal')->nullable()->after('alasan_permintaan');
        });
    }

    public function down(): void
    {
        Schema::table('purchaseros', function (Blueprint $table) {
            $table->dropColumn('nominal');
        });
    }
};
