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
        Schema::table('supplier', function (Blueprint $table) {
            $table->dropColumn(['nama_barang', 'jumlah_barang', 'harga_barang']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier', function (Blueprint $table) {
            $table->string('nama_barang')->nullable();
            $table->integer('jumlah_barang')->nullable();
            $table->decimal('harga_barang', 15, 2)->nullable();
        });
    }
};
