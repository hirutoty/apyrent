<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resign_offboardings', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pegawai');                  // Nama Pegawai
            $table->string('jabatan_terakhir');              // Jabatan Terakhir
            $table->date('tanggal_resign');                  // Tanggal Resign
            $table->string('alasan');                        // Alasan Resign
            $table->string('status_offboarding');            // Status Offboarding (Proses, Selesai)
            $table->string('serah_terima');                  // Serah Terima (Sudah/Belum)
            $table->text('keterangan')->nullable();          // Keterangan tambahan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resign_offboardings');
    }
};
