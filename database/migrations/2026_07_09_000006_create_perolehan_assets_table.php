<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perolehan_assets', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_perolehan');
            $table->string('kode_aset');
            $table->string('nama_aset');
            $table->string('vendor');
            $table->string('metode_pembelian');
            $table->decimal('harga', 15, 2);
            $table->string('status');
            $table->string('pembayaran');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perolehan_assets');
    }
};
