<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departemens', function (Blueprint $table) {
            $table->id();
            $table->string('nama_departemen');                         // Nama departemen
            $table->string('kepala_departemen');                       // Kepala departemen
            $table->date('tanggal_dibentuk');                          // Tanggal departemen dibentuk
            $table->integer('jumlah_posisi');                          // Jumlah posisi jabatan
            $table->text('keterangan')->nullable();                    // Keterangan tambahan
            $table->enum('status_aktif', ['Aktif', 'Non-Aktif']);      // Status aktif
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departemens');
    }
};
