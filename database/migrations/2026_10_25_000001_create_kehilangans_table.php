<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kehilangans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('kendaraan_id');
            $table->enum('status', ['sementara', 'permanen', 'ditemukan'])->default('sementara');
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->text('lokasi_kejadian')->nullable();
            $table->unsignedBigInteger('pelanggan_id')->nullable();
            $table->string('pengurus')->nullable();
            $table->text('informasi_gps')->nullable();
            $table->string('file')->nullable();
            $table->timestamps();

            $table->foreign('kendaraan_id')->references('id')->on('kendaraan')->onDelete('cascade');
            $table->foreign('pelanggan_id')->references('id')->on('member')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kehilangans');
    }
};
