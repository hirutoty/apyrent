<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gps_kendaraan_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('gps_kendaraan_id')
                ->constrained('gps_kendaraan')
                ->cascadeOnDelete();

            $table->foreignId('kendaraan_id')
                ->constrained('kendaraan')
                ->cascadeOnDelete();

            $table->foreignId('gps_id')
                ->constrained('gps')
                ->cascadeOnDelete();

            $table->string('type');
            $table->string('status_gps');
            $table->date('tanggal_pasang');
            $table->date('tanggal_habis');
            $table->integer('biaya_sewa');
            $table->integer('durasi_bulan');
            $table->string('status_sewa');
            $table->string('bukti_bayar')->nullable();
            $table->timestamp('diperpanjang_pada')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gps_kendaraan_histories');
    }
};