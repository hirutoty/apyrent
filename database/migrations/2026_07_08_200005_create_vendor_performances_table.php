<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_performances', function (Blueprint $table) {
            $table->id();
            $table->string('vendor');
            $table->integer('total_order')->default(0);
            $table->decimal('ketepatan_waktu', 5, 2)->default(0)->comment('persen');
            $table->decimal('kualitas_barang', 5, 2)->default(0)->comment('skala 1-100');
            $table->integer('komplain')->default(0);
            $table->decimal('penilaian_akhir', 5, 2)->default(0)->comment('skala 1-100');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_performances');
    }
};
