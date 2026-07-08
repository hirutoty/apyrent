<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penawarans', function (Blueprint $table) {
            $table->id();
            $table->string('no_quotation')->unique();
            $table->date('tanggal');
            $table->string('pelanggan');
            $table->string('produk_jasa');
            $table->integer('jumlah');
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('total_harga', 15, 2);
            $table->string('status');
            $table->date('valid_sampai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penawarans');
    }
};
