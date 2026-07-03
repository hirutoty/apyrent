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
        Schema::create('purchaseros', function (Blueprint $table) {
            $table->id();
            $table->string('no_pr')->unique()->nullable();
            $table->date('tanggal')->nullable();
            $table->string('departemen')->nullable();
            $table->string('pemohon')->nullable();
            $table->string('barang_jasa')->nullable();
            $table->string('kode_barang')->nullable();
            $table->integer('qty')->nullable();
            $table->string('satuan')->nullable();
            $table->string('alasan_permintaan')->nullable();
            $table->string('status')->nullable();
            $table->string('disetujui_oleh')->nullable();
            $table->date('tanggal_persetujuan')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchaseros');
    }
};