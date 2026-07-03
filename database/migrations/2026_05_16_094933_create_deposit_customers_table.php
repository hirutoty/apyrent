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
      Schema::create('deposit_customers', function (Blueprint $table) {
    $table->id();

    $table->foreignId('rental_id')
        ->constrained('rentals')
        ->cascadeOnDelete();

    $table->decimal('nominal', 15, 2);

    $table->decimal('potongan', 15, 2)->default(0);

    $table->enum('status', [
        'ditahan',
        'dikembalikan',
        'dipotong'
    ])->default('ditahan');

    $table->date('tanggal_deposit');

    $table->text('keterangan')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposit_customers');
    }
};
