<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kampanyes', function (Blueprint $table) {
            $table->id();
            $table->string('id_kampanye')->unique();
            $table->string('nama_kampanye');
            $table->string('tipe_kampanye');
            $table->string('channel');
            $table->string('target_segment');
            $table->date('tanggal_mulai');
            $table->date('tanggal_akhir');
            $table->string('subjek_pesan');
            $table->text('isi_pesan_ringkas');
            $table->string('status')->default('Dijadwalkan');
            $table->string('pic')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kampanyes');
    }
};
