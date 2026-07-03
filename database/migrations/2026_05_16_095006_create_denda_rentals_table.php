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
        Schema::create('denda_rentals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rental_id')
                ->constrained('rentals')
                ->cascadeOnDelete();

            $table->string('jenis');
            // telat, bbm, kerusakan, dll

            $table->decimal('nominal', 15, 2);

            $table->date('tanggal_denda');

            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('denda_rentals');
    }
};
