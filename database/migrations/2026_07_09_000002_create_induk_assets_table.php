<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('induk_assets', function (Blueprint $table) {
            $table->id();
            $table->string('kode_aset')->unique();
            $table->string('nama_aset');
            $table->string('kategori');
            $table->string('lokasi');
            $table->date('tanggal_perolehan');
            $table->bigInteger('harga_perolehan');
            $table->string('status')->default('Aktif');
            $table->string('pic');
            $table->integer('umur_ekonomis');
            $table->string('metode_penyusutan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('induk_assets');
    }
};
