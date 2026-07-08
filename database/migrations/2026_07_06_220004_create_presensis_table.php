<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presensis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pegawai');                                        // Nama Pegawai
            $table->date('tanggal');                                               // Tanggal presensi
            $table->time('jam_masuk');                                             // Jam Masuk
            $table->time('jam_pulang');                                            // Jam Pulang
            $table->string('metode_presensi');                                     // Metode (GPS, Face ID, dll)
            $table->string('lokasi_presensi');                                     // Lokasi presensi
            $table->enum('status', ['Hadir', 'Alpa', 'Izin', 'Terlambat']);        // Status kehadiran
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presensis');
    }
};
