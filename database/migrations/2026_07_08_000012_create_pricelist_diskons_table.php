<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricelist_diskons', function (Blueprint $table) {
            $table->id();
            $table->string('id_harga')->unique();
            $table->string('nama_produk');
            $table->string('level_pelanggan');
            $table->decimal('harga_normal', 15, 2);
            $table->decimal('diskon', 5, 2)->default(0);
            $table->decimal('harga_diskon', 15, 2);
            $table->date('periode_mulai');
            $table->date('periode_selesai');
            $table->string('status')->default('Aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricelist_diskons');
    }
};
