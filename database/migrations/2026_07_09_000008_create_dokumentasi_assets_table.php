<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumentasi_assets', function (Blueprint $table) {
            $table->id();
            $table->string('kode_aset');
            $table->string('nama_aset');
            $table->boolean('foto_tersimpan')->default(false);
            $table->string('lokasi_file')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumentasi_assets');
    }
};
