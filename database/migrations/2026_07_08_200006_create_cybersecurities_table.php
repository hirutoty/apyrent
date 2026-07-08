<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cybersecurities', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('kategori'); // Insiden / Vulnerability / Audit / Pentest / Patch
            $table->string('tingkat_risiko'); // Low / Medium / High / Critical
            $table->text('deskripsi');
            $table->string('sistem_terdampak')->nullable();
            $table->date('tanggal_temuan');
            $table->date('tanggal_penanganan')->nullable();
            $table->string('penanggungjawab')->nullable();
            $table->text('tindakan')->nullable();
            $table->string('status'); // Open / In Progress / Resolved / Accepted Risk
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cybersecurities');
    }
};
