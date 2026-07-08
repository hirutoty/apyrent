<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no')->unique();
            $table->date('tanggal');
            $table->string('pelanggan');
            $table->string('produk_jasa');
            $table->integer('qty');
            $table->decimal('total_harga', 15, 2);
            $table->string('status_order');
            $table->string('metode_pembayaran');
            $table->string('sales');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_orders');
    }
};
