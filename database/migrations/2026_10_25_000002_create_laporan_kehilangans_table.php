<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_kehilangans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('kehilangan_id');
            $table->text('laporan');
            $table->date('tanggal_laporan');
            $table->text('keterangan')->nullable();
            $table->text('lokasi_pencarian')->nullable();
            $table->string('file')->nullable();
            $table->timestamps();

            $table->foreign('kehilangan_id')->references('id')->on('kehilangans')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_kehilangans');
    }
};
