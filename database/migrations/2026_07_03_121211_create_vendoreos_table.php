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
        Schema::create('vendoreos', function (Blueprint $table) {
            $table->id();
            $table->string('kode_vendor')->nullable();
            $table->string('nama_vendor')->nullable();
            $table->string('kategori')->nullable();
            $table->string('alamat')->nullable();
            $table->string('pic_vendor')->nullable();
            $table->string('no_telp')->nullable();
            $table->string('produk_jasa')->nullable();
            $table->integer('rating')->nullable(); // Skala 1-5
            $table->string('status')->nullable(); // Aktif / Tidak Aktif
            $table->date('tanggal_terakhir_order')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendoreos');
    }
};