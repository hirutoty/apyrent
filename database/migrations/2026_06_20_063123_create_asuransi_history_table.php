<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asuransi_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asuransi_kendaraan_id')->constrained('asuransi_kendaraan')->onDelete('cascade');
            $table->foreignId('kendaraan_id')->constrained('kendaraan')->onDelete('cascade');
            $table->foreignId('asuransi_id')->constrained('asuransi')->onDelete('cascade');
            $table->foreignId('jenis_asuransi_id')->constrained('jenis_asuransi')->onDelete('cascade');
            $table->date('tgl_mulai');
            $table->date('tgl_berakhir');
            $table->integer('durasi_bulan');
            $table->decimal('biaya', 15, 2);
            $table->string('bukti_bayar')->nullable();
            $table->string('status_kendaraan')->default('aktif');
            $table->timestamp('diperpanjang_pada')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asuransi_history');
    }
};