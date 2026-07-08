<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('daftar_notaris', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kantor');
            $table->string('layanan');
            $table->string('kontak');
            $table->string('email');
            $table->date('terakhir_dipakai')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('daftar_notaris');
    }
};
