<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('komisi_sales', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sales');
            $table->string('bulan');
            $table->decimal('total_penjualan', 15, 2);
            $table->decimal('persen_komisi', 5, 2)->default(0);
            $table->decimal('total_komisi', 15, 2);
            $table->string('status_bayar')->default('Belum Dibayar');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('komisi_sales');
    }
};
