<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('policy_compliances', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kebijakan');
            $table->string('kategori'); // Keamanan / Data / Operasional / Legal / Audit
            $table->text('deskripsi')->nullable();
            $table->string('versi')->nullable();
            $table->date('tanggal_efektif');
            $table->date('tanggal_review')->nullable();
            $table->string('pengesah')->nullable();
            $table->string('departemen_terkait')->nullable();
            $table->string('standar_acuan')->nullable(); // ISO 27001 / PCI-DSS / GDPR / dll
            $table->string('status'); // Draft / Aktif / Review / Archived
            $table->string('dokumen')->nullable(); // path file
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('policy_compliances');
    }
};
