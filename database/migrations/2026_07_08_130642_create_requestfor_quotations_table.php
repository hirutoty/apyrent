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
        Schema::create('requestfor_quotations', function (Blueprint $table) {
          $table->id();
            $table->string('id_rfq');
            $table->date('tanggal_rfq');
            $table->string('vendor');
            $table->string('kode_barang');
            $table->string('nama_barang');
            $table->integer('kuantitas');
            $table->string('satuan');
            $table->bigInteger('harga_estimasi');
            $table->date('tanggal_kirim');
            $table->string('status_rfq');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requestfor_quotations');
    }
};
