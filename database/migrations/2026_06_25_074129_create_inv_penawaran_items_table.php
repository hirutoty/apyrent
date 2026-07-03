<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inv_penawaran_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('penawaran_id')->nullable();
            $table->foreign('penawaran_id')
                  ->references('id')->on('inv_penawarans')
                  ->onDelete('cascade');

              // Relasi ke tabel kendaraans
            $table->foreignId('kendaraan_id')
                ->nullable()
                ->constrained('kendaraan')
                ->nullOnDelete();
            $table->unsignedInteger('qty')->default(1)->nullable();
            $table->text('tahun_unit')->nullable();      // tahun kendaraan
            $table->decimal('price', 15, 2)->default(0)->nullable();  // harga sewa / unit
            $table->unsignedInteger('durasi')->default(1)->nullable(); // angka durasi
            $table->text('satuan_durasi')->nullable();   // day | month | year
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inv_penawaran_items');
    }
};