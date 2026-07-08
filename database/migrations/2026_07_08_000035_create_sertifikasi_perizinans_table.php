<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sertifikasi_perizinans', function (Blueprint $table) {
            $table->id();
            $table->string('jenis');
            $table->string('nomor');
            $table->string('instansi');
            $table->string('berlaku_hingga');
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sertifikasi_perizinans');
    }
};
