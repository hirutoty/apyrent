<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_plannings', function (Blueprint $table) {
            $table->id();
            $table->string('kode_proyek');
            $table->string('tahapan');
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->integer('durasi')->default(0);
            $table->string('pic');
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_plannings');
    }
};
