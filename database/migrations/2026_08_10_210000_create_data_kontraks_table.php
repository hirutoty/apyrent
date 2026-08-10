<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_kontraks', function (Blueprint $table) {
            $table->id();

            // No kontrak — auto-generate (KTR-YYYYMM-XXXX)
            $table->string('no_kontrak')->unique();

            // Relasi ke kendaraan (nullable, bisa diisi manual)
            $table->unsignedBigInteger('kendaraan_id')->nullable();
            $table->foreign('kendaraan_id')
                  ->references('id')->on('kendaraan')
                  ->onDelete('set null');

            // Auto-fill dari kendaraan
            $table->string('mobil')->nullable();
            $table->string('nopol')->nullable();
            $table->string('tahun')->nullable();

            // Data pengguna / customer
            $table->string('user_kontrak')->nullable();

            // Cicilan
            $table->bigInteger('angsuran_per_bulan')->default(0);
            $table->unsignedTinyInteger('jatuh_tempo')->nullable(); // tanggal tiap bulan (1–31)

            // Periode
            $table->date('periode_mulai')->nullable();
            $table->date('periode_selesai')->nullable();

            // Info tambahan
            $table->string('personal_account')->nullable();   // opsional
            $table->string('sumber_dana_debit')->nullable();
            $table->string('cara_bayar')->nullable();
            $table->string('asuransi_leasing')->nullable();   // teks bebas

            // Bukti (single file, opsional)
            $table->text('bukti')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_kontraks');
    }
};
