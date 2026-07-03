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
        Schema::create('rekonsiliasi_bank', function (Blueprint $table) {

            $table->id();

            $table->date('tanggal')->nullable();

            $table->string('deskripsi')->nullable();

            $table->string('reference_no')->nullable();

            $table->decimal('amount', 15, 2)->nullable();

            $table->string('currency', 10)
                ->default('IDR');

            $table->enum('status_rekonsiliasi', [
                'matched',
                'unmatched',
                'Pending'
            ])->default('Pending');

            $table->unsignedBigInteger('invoice_id')
                ->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekonsiliasi_bank');
    }
};