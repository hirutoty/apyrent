<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('litigasis', function (Blueprint $table) {
            $table->id();
            $table->string('kasus');
            $table->string('lawan');
            $table->string('jenis_kasus');
            $table->string('status');
            $table->string('pengacara');
            $table->date('tanggal_sidang');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('litigasis');
    }
};
