<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_management', function (Blueprint $table) {
            $table->id();
            $table->string('kode_proyek')->unique();
            $table->string('nama_proyek');
            $table->text('deskripsi')->nullable();
            $table->string('kategori')->nullable(); // Internal / External / Infrastruktur / Aplikasi
            $table->string('pic'); // Person in Charge
            $table->string('tim')->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->integer('progress')->default(0); // 0-100 persen
            $table->bigInteger('anggaran')->default(0);
            $table->bigInteger('realisasi')->default(0);
            $table->string('prioritas'); // Low / Medium / High
            $table->string('status'); // Planning / In Progress / On Hold / Completed / Cancelled
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_management');
    }
};
