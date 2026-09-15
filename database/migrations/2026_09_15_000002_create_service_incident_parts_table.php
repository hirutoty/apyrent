<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_incident_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_incident_id')->constrained('service_incidents')->cascadeOnDelete();
            $table->foreignId('kendaraan_id')->constrained('kendaraan')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('service_categories')->nullOnDelete();
            $table->string('nama_part');
            $table->string('part_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('posisi')->nullable();
            $table->date('tgl_pasang');
            $table->unsignedInteger('kilometer_pasang')->nullable();
            $table->enum('kondisi', ['Baik', 'Rusak', 'Perlu Ganti'])->default('Baik');
            $table->string('status')->default('Proses'); // Proses, Terpasang, Diganti, Limit, tidak_aktif
            $table->unsignedSmallInteger('interval_nilai')->default(12);
            $table->enum('interval_satuan', ['hari', 'minggu', 'bulan', 'tahun'])->default('bulan');
            $table->date('tanggal_limit')->nullable();
            $table->unsignedBigInteger('biaya')->default(0);
            $table->json('bukti')->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('persetujuan', ['Pending', 'Disetujui', 'Ditolak'])->default('Pending');
            $table->string('nama_rekening')->nullable();
            $table->string('nama_bank')->nullable();
            $table->string('no_rekening')->nullable();
            $table->timestamp('replaced_at')->nullable();
            $table->unsignedBigInteger('replaced_by_part_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_incident_parts');
    }
};
