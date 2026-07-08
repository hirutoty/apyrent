<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_appraisals', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pegawai');                  // Nama Pegawai
            $table->string('periode_evaluasi');              // Periode Evaluasi (misal: Q1 2026)
            $table->integer('disiplin');                     // Nilai Disiplin (0-100)
            $table->integer('kolaborasi');                   // Nilai Kolaborasi (0-100)
            $table->integer('produktivitas');                // Nilai Produktivitas (0-100)
            $table->decimal('nilai_akhir', 5, 2);            // Nilai Akhir rata-rata
            $table->string('evaluator');                     // Nama Evaluator
            $table->text('catatan')->nullable();              // Catatan Evaluator
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_appraisals');
    }
};
