<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shift_lemburs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pegawai');                  // Nama Pegawai
            $table->string('shift');                         // Shift (Pagi, Siang, Malam)
            $table->time('jam_masuk');                       // Jam Masuk
            $table->time('jam_pulang');                      // Jam Pulang
            $table->string('jam_lembur')->nullable();        // Jam Lembur (nullable)
            $table->string('total_jam');                     // Total Jam Kerja
            $table->string('keterangan');                    // Keterangan Lembur / Catatan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift_lemburs');
    }
};
