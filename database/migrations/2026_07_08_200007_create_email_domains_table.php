<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_domains', function (Blueprint $table) {
            $table->id();
            $table->string('nama_domain');
            $table->string('registrar')->nullable();
            $table->date('tanggal_registrasi')->nullable();
            $table->date('tanggal_expired')->nullable();
            $table->bigInteger('harga_perpanjangan')->default(0);
            $table->string('nameserver_1')->nullable();
            $table->string('nameserver_2')->nullable();
            $table->string('hosting_provider')->nullable();
            $table->string('ssl_status')->nullable(); // Aktif / Expired / Tidak Ada
            $table->date('ssl_expired')->nullable();
            $table->string('pengelola')->nullable();
            $table->string('status'); // Aktif / Expired / Suspended
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_domains');
    }
};
