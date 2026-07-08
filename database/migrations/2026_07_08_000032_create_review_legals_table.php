<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('review_legals', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('pemohon');
            $table->string('dokumen');
            $table->string('status_review');
            $table->string('pic_legal');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('review_legals');
    }
};
