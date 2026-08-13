<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_parts', function (Blueprint $table) {
            $table->id();

            // Relasi ke header service
            $table->foreignId('service_history_id')
                ->constrained('service_history')
                ->cascadeOnDelete();

            // Relasi ke kendaraan (denormalized untuk query cepat)
            $table->foreignId('kendaraan_id')
                ->constrained('kendaraan')
                ->cascadeOnDelete();

            // Kategori part (FK ke service_categories, nullable agar fleksibel)
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('service_categories')
                ->nullOnDelete();

            // Identitas part
            $table->string('nama_part');
            $table->string('part_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('posisi')->nullable();

            // Data pemasangan
            $table->date('tgl_pasang');
            $table->bigInteger('kilometer_pasang')->default(0);

            // Kondisi & status
            $table->enum('kondisi', ['Baik', 'Rusak', 'Perlu Ganti'])->default('Baik');
            $table->enum('status', ['Terpasang', 'Limit'])->default('Terpasang');

            // Interval untuk pengecekan limit berbasis waktu
            $table->integer('interval_nilai')->default(1);
            $table->enum('interval_satuan', ['hari', 'minggu', 'bulan', 'tahun'])->default('bulan');

            // Tanggal limit dihitung: tgl_pasang + interval (disimpan agar cron cepat)
            $table->date('tanggal_limit')->nullable();

            // Biaya pemasangan/penggantian part ini
            $table->bigInteger('biaya')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_parts');
    }
};
