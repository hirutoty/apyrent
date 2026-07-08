<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('struktur_organisasis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jabatan');                        // Nama jabatan
            $table->string('nama_pegawai');                        // Nama pegawai
            $table->string('nip_id');                              // NIP/ID Pegawai
            $table->string('departemen');                          // Departemen tempat pegawai bekerja
            $table->string('atasan_langsung')->nullable();         // Atasan langsung
            $table->string('lokasi');                              // Lokasi kerja
            $table->enum('status_jabatan', ['Tetap', 'Kontrak']); // Status jabatan
            $table->date('tanggal_mulai');                         // Tanggal mulai jabatan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('struktur_organisasis');
    }
};
