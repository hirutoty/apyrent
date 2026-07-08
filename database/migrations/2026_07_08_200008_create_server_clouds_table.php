<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('server_clouds', function (Blueprint $table) {
            $table->id();
            $table->string('nama_server');
            $table->string('tipe'); // Physical / VPS / Cloud / Dedicated
            $table->string('provider')->nullable(); // AWS / GCP / Azure / Niagahoster / dll
            $table->string('ip_address')->nullable();
            $table->string('os')->nullable();
            $table->string('cpu')->nullable();
            $table->string('ram')->nullable();
            $table->string('storage')->nullable();
            $table->string('fungsi')->nullable(); // Web Server / DB Server / File Server / dll
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_expired')->nullable();
            $table->bigInteger('biaya_bulanan')->default(0);
            $table->string('pengelola')->nullable();
            $table->string('status'); // Aktif / Maintenance / Down / Terminated
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('server_clouds');
    }
};
