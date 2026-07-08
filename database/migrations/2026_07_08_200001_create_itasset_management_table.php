<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('itasset_management', function (Blueprint $table) {
            $table->id();
            $table->string('kode_asset')->unique();
            $table->string('nama_asset');
            $table->string('kategori'); // Hardware / Software / Network / Peripheral
            $table->string('merk')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->date('tanggal_pembelian')->nullable();
            $table->bigInteger('harga_pembelian')->default(0);
            $table->string('vendor')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('pemegang_asset')->nullable();
            $table->string('departemen')->nullable();
            $table->date('masa_garansi')->nullable();
            $table->string('status'); // Aktif / Tidak Aktif / Rusak / Disposed
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itasset_management');
    }
};
