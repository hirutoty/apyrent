<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skill_matrices', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pegawai');                  // Nama pegawai
            $table->string('skill');                         // Nama skill
            $table->integer('level')->unsigned();            // Level skill (1-5)
            $table->enum('sertifikasi', ['Y', 'T']);         // Bersertifikasi atau tidak
            $table->string('evaluator');                     // Nama evaluator
            $table->date('tanggal_evaluasi');                // Tanggal evaluasi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skill_matrices');
    }
};
