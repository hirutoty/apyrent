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
        Schema::create('gps_kendaraan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kendaraan_id')
                ->constrained('kendaraan')
                ->cascadeOnDelete();

            $table->foreignId('gps_id')
                ->constrained('gps')
                ->cascadeOnDelete();

            $table->string('type')->nullable();

            $table->enum('status_gps', [
                'aktif',
                'nonaktif'
            ])->default('aktif');

            $table->date('tanggal_pasang');
            $table->date('tanggal_habis');

            $table->bigInteger('biaya_sewa')->default(0);
            $table->integer('durasi_bulan')->default(0);

            $table->enum('status_sewa', [
                'aktif',
                'habis'
            ])->default('aktif');

            $table->string('bukti_bayar')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gps_kendaraans');
    }
};
