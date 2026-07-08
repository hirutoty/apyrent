<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hutang_vendors', function (Blueprint $table) {
            $table->id();
            $table->string('nama_vendor');
            $table->string('kategori');
            $table->decimal('nominal', 15, 2)->default(0);
            $table->decimal('dibayar', 15, 2)->default(0);
            $table->decimal('sisa', 15, 2)->default(0);
            $table->date('jatuh_tempo');
            $table->enum('status', ['lunas', 'belum_lunas'])->default('belum_lunas');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hutang_vendors');
    }
};
