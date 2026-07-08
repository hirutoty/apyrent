<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('software_licenses', function (Blueprint $table) {
            $table->id();
            $table->string('nama_software');
            $table->string('vendor');
            $table->string('tipe_lisensi'); // Perpetual / Subscription / Open Source
            $table->string('license_key')->nullable();
            $table->integer('jumlah_lisensi')->default(1);
            $table->integer('lisensi_terpakai')->default(0);
            $table->date('tanggal_pembelian')->nullable();
            $table->date('tanggal_expired')->nullable();
            $table->bigInteger('harga')->default(0);
            $table->string('pengguna')->nullable();
            $table->string('departemen')->nullable();
            $table->string('status'); // Aktif / Expired / Tidak Aktif
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('software_licenses');
    }
};
