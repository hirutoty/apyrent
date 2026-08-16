<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('service_parts', function (Blueprint $table) {
            $table->text('bukti')->nullable()->after('biaya');
            $table->text('keterangan')->nullable()->after('bukti');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_parts', function (Blueprint $table) {
            $table->dropColumn(['bukti', 'keterangan']);
        });
    }
};
