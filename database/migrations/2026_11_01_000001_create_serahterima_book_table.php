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
        Schema::create('serahterima_book', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_rental');
            $table->date('tanggal');
            $table->string('file_masuk')->nullable();
            $table->string('file_keluar')->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('id_rental')->references('id')->on('rentals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('serahterima_book');
    }
};
