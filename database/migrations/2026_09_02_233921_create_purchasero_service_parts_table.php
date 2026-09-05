<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran_service_parts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pembayaran_id')
                ->constrained('pembayarans')
                ->cascadeOnDelete();

            $table->foreignId('kendaraan_id')
                ->constrained('kendaraan')
                ->cascadeOnDelete();

            $table->foreignId('category_id')
                ->nullable()
                ->constrained('service_categories')
                ->nullOnDelete();

            $table->string('nama_part');
            $table->string('part_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('posisi')->nullable();
            $table->string('merk')->nullable();

            $table->date('tgl_pasang');
            $table->bigInteger('kilometer_pasang')->default(0);

            $table->enum('kondisi', ['Baik', 'Rusak', 'Perlu Ganti'])->default('Baik');
            $table->enum('status_part', ['Proses', 'Terpasang', 'Ditolak'])->default('Proses');

            $table->integer('interval_nilai')->default(1);
            $table->enum('interval_satuan', ['hari', 'minggu', 'bulan', 'tahun'])->default('bulan');

            $table->bigInteger('biaya')->default(0);
            $table->text('keterangan')->nullable();

            // Flag apakah kendaraan sudah melewati service limit saat PR dibuat
            $table->boolean('is_over_limit')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_service_parts');
    }
};
