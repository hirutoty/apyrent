<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devops', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pipeline');
            $table->string('aplikasi');
            $table->string('environment'); // Development / Staging / Production
            $table->string('tipe'); // CI / CD / CI-CD / Monitoring / IaC
            $table->string('tools')->nullable(); // Jenkins / GitLab CI / GitHub Actions / dll
            $table->string('repository')->nullable();
            $table->string('branch')->nullable();
            $table->string('trigger')->nullable(); // Push / Schedule / Manual
            $table->date('terakhir_deploy')->nullable();
            $table->string('penanggung_jawab')->nullable();
            $table->string('status'); // Aktif / Nonaktif / Error / Pending
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devops');
    }
};
