<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retur_penjualans', function (Blueprint $table) {
            $table->id();
            $table->string('no_retur')->unique();
            $table->date('tanggal');
            $table->string('no_order');
            $table->string('pelanggan');
            $table->string('produk');
            $table->integer('qty');
            $table->string('alasan');
            $table->decimal('nilai_retur', 15, 2);
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retur_penjualans');
    }
};
