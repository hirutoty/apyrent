<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kir_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kir_id')->constrained('kir')->onDelete('cascade');
            $table->foreignId('kendaraan_id')->constrained('kendaraan')->onDelete('cascade');
            $table->string('no_uji');
            $table->date('masa_berlaku');
            $table->decimal('biaya', 15, 2);
            $table->string('image')->nullable();
            $table->timestamp('diperpanjang_pada')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kir_history');
    }
};