<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stnk_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('stnk_id')
                ->constrained('stnk')
                ->cascadeOnDelete();

            $table->foreignId('kendaraan_id')
                ->constrained('kendaraan')
                ->cascadeOnDelete();

            $table->string('nopol');
            $table->string('merk')->nullable();
            $table->string('nama_pemilik')->nullable();
            $table->string('jenis_model')->nullable();
            $table->date('masa_berlaku');
            $table->decimal('biaya', 15, 2)->default(0);
            $table->string('bukti')->nullable();
            $table->timestamp('diperpanjang_pada')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stnk_histories');
    }
};