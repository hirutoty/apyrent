<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_accesses', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pengguna');
            $table->string('username');
            $table->string('email')->nullable();
            $table->string('departemen')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('sistem_aplikasi'); // Sistem / aplikasi yang diakses
            $table->string('level_akses'); // Read / Read-Write / Admin / Full
            $table->date('tanggal_pemberian');
            $table->date('tanggal_berakhir')->nullable();
            $table->string('pemberi_akses')->nullable();
            $table->string('status'); // Aktif / Tidak Aktif / Suspended
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_accesses');
    }
};
