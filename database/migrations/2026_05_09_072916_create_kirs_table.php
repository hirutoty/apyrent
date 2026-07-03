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
        Schema::create('kir', function (Blueprint $table) {
    $table->id();

    $table->foreignId('kendaraan_id')
        ->constrained('kendaraan')
        ->cascadeOnDelete();

    $table->string('no_uji');
    $table->date('masa_berlaku');
     
    $table->decimal('biaya', 15, 2)->default(0);

    $table->string('image')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kirs');
    }
};
