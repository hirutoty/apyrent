<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('target_penjualans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sales');
            $table->string('bulan');
            $table->decimal('target_penjualan', 15, 2);
            $table->decimal('pencapaian', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('target_penjualans');
    }
};
