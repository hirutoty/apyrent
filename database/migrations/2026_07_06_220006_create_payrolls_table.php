<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pegawai');                  // Nama Pegawai
            $table->decimal('gaji_pokok', 15, 2);            // Gaji Pokok
            $table->decimal('tunjangan', 15, 2);             // Tunjangan
            $table->decimal('thr', 15, 2);                   // THR
            $table->decimal('bpjs', 15, 2);                  // BPJS
            $table->decimal('pph21', 15, 2);                 // PPh21
            $table->decimal('total_gaji', 15, 2);            // Total Gaji
            $table->string('slip_gaji')->nullable();          // Slip Gaji (file path atau keterangan)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
