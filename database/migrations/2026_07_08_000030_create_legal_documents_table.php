<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('legal_documents', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama_dokumen');
            $table->string('jenis');
            $table->string('pihak_terkait');
            $table->date('tgl_terbit');
            $table->date('berlaku_hingga')->nullable();
            $table->string('status');
            $table->string('format');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('legal_documents');
    }
};
