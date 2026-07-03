<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kendaraan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('jenis_id')->constrained()->cascadeOnDelete();

            $table->string('nopol');
            $table->string('foto')->nullable();

            $table->string('nama_pemilik');
            $table->text('alamat')->nullable();

            $table->string('merk');

            $table->year('tahun_pembuatan')->nullable();
            $table->year('tahun_perakitan')->nullable();

            $table->string('isi_silinder')->nullable();
            $table->string('warna')->nullable();

            $table->string('no_rangka')->nullable();
            $table->string('no_mesin')->nullable();
            $table->string('no_bpkb')->nullable();

            $table->string('warna_tnkb')->nullable();
            $table->string('bahan_bakar')->nullable();

            $table->string('kode_lokasi')->nullable();
            $table->string('no_urut_pendaftaran')->nullable();

            // 💰 BIAYA UTAMA KENDARAAN
            $table->bigInteger('harga_sewa_per_hari')->default(0);
            $table->bigInteger('harga_sewa_per_jam')->default(0);

            $table->bigInteger('batas_biaya')->default(0);

            $table->string('dokumen')->nullable();
            $table->date('masa_berlaku')->nullable();

            $table->integer('kilometer_sekarang')->default(0);

            $table->integer('limit_km_service')->default(0);
            $table->integer('limit_bulan_service')->default(0);
            $table->integer('km_terakhir_service')->default(0);

            $table->date('tanggal_terakhir_service')->nullable();

            $table->enum('status_service', ['aman', 'service'])->default('aman');
            $table->enum('status_kendaraan', ['tersedia', 'disewa', 'service', 'bermasalah'])->default('tersedia');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kendaraan');
    }
};