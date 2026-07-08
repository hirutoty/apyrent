<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_backups', function (Blueprint $table) {
            $table->id();
            $table->string('nama_backup');
            $table->string('tipe_backup'); // Full / Incremental / Differential
            $table->string('target_sistem'); // Sistem / database / folder yang di-backup
            $table->string('media_penyimpanan'); // Local / NAS / Cloud / Tape
            $table->string('lokasi_backup')->nullable();
            $table->date('tanggal_backup');
            $table->time('waktu_backup')->nullable();
            $table->string('ukuran')->nullable(); // misal: 2 GB
            $table->string('frekuensi')->nullable(); // Daily / Weekly / Monthly / Manual
            $table->string('penanggung_jawab')->nullable();
            $table->string('status'); // Sukses / Gagal / Partial
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_backups');
    }
};
