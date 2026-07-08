<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hrd_files', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pegawai');                  // Nama Pegawai pemilik file
            $table->string('nama_file');                     // Nama/label file
            $table->string('jenis_dokumen');                 // Jenis dokumen (KTP, Ijazah, SK, dll)
            $table->string('file_path');                     // Path file yang diupload
            $table->text('keterangan')->nullable();          // Keterangan tambahan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrd_files');
    }
};
