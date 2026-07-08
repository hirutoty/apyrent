<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalties', function (Blueprint $table) {
            $table->id();
            $table->string('id_program')->unique();
            $table->string('nama_program');
            $table->string('jenis_reward');
            $table->string('akumulasi_poin');
            $table->string('konversi_poin');
            $table->date('periode_mulai');
            $table->date('periode_akhir');
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalties');
    }
};
