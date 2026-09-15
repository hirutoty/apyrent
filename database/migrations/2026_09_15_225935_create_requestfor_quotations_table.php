<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requestfor_quotations', function (Blueprint $table) {
            $table->id();
            $table->string('id_rfq')->unique();
            $table->date('tanggal_rfq');
            $table->string('vendor');
            $table->string('kode_barang')->nullable();
            $table->string('nama_barang');
            $table->integer('kuantitas');
            $table->string('satuan')->nullable();
            $table->integer('harga_estimasi')->nullable();
            $table->date('tanggal_kirim')->nullable();
            $table->string('status_rfq')->default('Open');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requestfor_quotations');
    }
};
