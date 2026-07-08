<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuti_izins', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pegawai');                  // Nama Pegawai
            $table->string('jenis_cuti_izin');               // Jenis Cuti/Izin (Cuti Tahunan, Sakit, dll)
            $table->date('tanggal_mulai');                   // Tanggal Mulai
            $table->date('tanggal_selesai');                 // Tanggal Selesai
            $table->integer('lama_hari');                    // Lama Hari
            $table->text('alasan');                          // Alasan
            $table->string('status');                        // Status (Disetujui, Ditolak, Pending)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuti_izins');
    }
};
