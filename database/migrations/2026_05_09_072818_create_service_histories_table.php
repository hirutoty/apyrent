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
        Schema::create('service_history', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kendaraan_id')
                ->constrained('kendaraan')
                ->cascadeOnDelete();

            $table->text('keluhan')->nullable();

            $table->integer('kilometer')->default(0);
            $table->bigInteger('total_biaya')->default(0);

            $table->enum('status', [
                'proses',
                'selesai'
            ])->default('proses');

            $table->string('bukti_pembayaran')->nullable(); // foto bukti pembayaran
            $table->bigInteger('maks_bulanan')->default(0);
            $table->bigInteger('biaya_tahunan')->default(0);
            $table->enum('status_pengeluaran', [
                'stabil',
                'overservice'
            ])->default('stabil');

            $table->date('tanggal_service');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_histories');
    }
};
