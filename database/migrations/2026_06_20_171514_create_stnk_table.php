<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stnk', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel kendaraan
            $table->foreignId('kendaraan_id')
                ->constrained('kendaraan')
                ->onDelete('cascade');

            // Snapshot data dari kendaraan
            $table->string('nopol');
            $table->string('merk');

            // Data STNK
            $table->string('nama_pemilik');
            $table->string('jenis_model');
            $table->date('masa_berlaku');
            $table->decimal('biaya', 15, 2)->default(0);
            $table->string('bukti');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stnk');
    }
};