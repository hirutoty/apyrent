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
        Schema::create('hutang_vendors', function (Blueprint $table) {
    $table->id();

    $table->string('nama_vendor');

    $table->string('kategori')->nullable();
    // bengkel, sparepart, leasing, dll

    $table->decimal('nominal', 15, 2);

    $table->decimal('dibayar', 15, 2)->default(0);

    $table->decimal('sisa', 15, 2);

    $table->date('jatuh_tempo')->nullable();

    $table->enum('status', [
        'belum_lunas',
        'cicilan',
        'lunas'
    ])->default('belum_lunas');

    $table->text('keterangan')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hutang_vendors');
    }
};
