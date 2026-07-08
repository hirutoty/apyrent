<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('induk_proyeks', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama_proyek');
            $table->string('jenis'); // Internal / Eksternal
            $table->string('klien_vendor')->nullable();
            $table->string('pic');
            $table->string('status'); // Plan / Berjalan / Approved / Selesai
            $table->date('mulai');
            $table->date('target_selesai');
            $table->string('progres')->default('0%');
            $table->bigInteger('nilai_proyek')->default(0);
            $table->string('lokasi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('induk_proyeks');
    }
};
