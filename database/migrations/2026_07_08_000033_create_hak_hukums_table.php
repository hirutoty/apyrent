<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('hak_hukums', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_akses');
            $table->string('kategori_dokumen');
            $table->string('penerima_akses');
            $table->string('level_hak');
            $table->date('tanggal_akses');
            $table->string('status')->default('Aktif');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hak_hukums');
    }
};
