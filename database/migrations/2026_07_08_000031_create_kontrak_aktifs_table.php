<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kontrak_aktifs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kontrak');
            $table->string('mitra');
            $table->unsignedBigInteger('nilai');
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->string('pic');
            $table->string('status');
            $table->boolean('perpanjangan')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kontrak_aktifs');
    }
};
