<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_assets', function (Blueprint $table) {
            $table->id();
            $table->string('kode_aset');
            $table->date('tanggal_audit');
            $table->string('diperiksa_oleh');
            $table->string('status_fisik');
            $table->string('temuan');
            $table->string('tindakan')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_assets');
    }
};
