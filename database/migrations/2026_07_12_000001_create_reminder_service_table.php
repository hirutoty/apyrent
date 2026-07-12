<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminder_service', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kendaraan_id')
                ->constrained('kendaraan')
                ->cascadeOnDelete();

            // Nama reminder bebas (Service Rutin, Accu, Ban, dll)
            $table->string('nama_reminder');

            // Tanggal mulai / tanggal terakhir dilakukan
            $table->date('tanggal_mulai');

            // Interval reminder
            $table->integer('interval_nilai')->default(1);
            $table->enum('interval_satuan', ['hari', 'minggu', 'bulan', 'tahun'])->default('bulan');

            // Tanggal jatuh tempo (dihitung otomatis: tanggal_mulai + interval)
            $table->date('tanggal_jatuh_tempo')->nullable();

            // Keterangan bebas (merek ban, SN, jenis, dll)
            $table->text('keterangan')->nullable();

            // Status reminder
            $table->enum('status', ['aktif', 'jatuh_tempo', 'selesai'])->default('aktif');

            // Apakah sudah auto-create ke service_detail
            $table->boolean('sudah_dibuat_masalah')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminder_service');
    }
};
