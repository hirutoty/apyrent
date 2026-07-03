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
    Schema::table('rentals', function (Blueprint $table) {
        $table->bigInteger('durasi_tahun')->nullable();
        $table->string('invoice')->nullable();
        $table->string('kelayakan')->nullable();
        
    });
}

public function down(): void
{
    Schema::table('rentals', function (Blueprint $table) {
        $table->dropColumn(['durasi_tahun', 'kelayakan', 'invoice']);
    });
}
};
