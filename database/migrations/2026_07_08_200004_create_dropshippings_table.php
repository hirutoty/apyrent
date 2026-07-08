<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dropshippings', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi');
            $table->string('tipe');
            $table->string('vendor');
            $table->string('barang');
            $table->integer('jumlah');
            $table->string('satuan');
            $table->string('customer_akhir');
            $table->date('tanggal_kirim')->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dropshippings');
    }
};
