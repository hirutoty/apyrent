<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_biaya_tambahan', function (Blueprint $table) {

            $table->id();

            $table->foreignId('rental_id')
                ->constrained('rentals')
                ->cascadeOnDelete();

            $table->foreignId('biaya_tambahan_id')
                ->constrained('biaya_tambahans')
                ->cascadeOnDelete();

            $table->integer('jumlah')->default(1);

            $table->bigInteger('subtotal')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_biaya_tambahan');
    }
};