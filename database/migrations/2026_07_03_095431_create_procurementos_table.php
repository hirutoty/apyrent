<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('procurementos', function (Blueprint $table) {
            $table->id();
            $table->string('workflow_id')->unique()->nullable();
            $table->string('nama_workflow')->nullable();
            $table->string('trigger_event')->nullable();
            $table->string('syarat_tambahan')->nullable();
            $table->string('aksi_dilakukan')->nullable();
            $table->string('delay_aksi')->nullable();
            $table->string('status')->nullable();
            $table->string('pic')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('procurementos');
    }
};