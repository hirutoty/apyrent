<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signature_dokumens', function (Blueprint $table) {
            $table->id();
            $table->string('document_id')->unique();
            $table->string('jenis_dokumen');
            $table->date('tanggal');
            $table->string('pihak_terlibat');
            $table->string('status_ttd');
            $table->string('platform_digisign');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signature_dokumens');
    }
};
