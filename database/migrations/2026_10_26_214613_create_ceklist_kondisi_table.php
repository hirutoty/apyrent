<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ceklist_kondisi', function (Blueprint $table) {
            $table->id();
            
            // Informasi Dasar
            $table->string('nama');
            $table->string('perusahaan');
            $table->text('alamat');
            $table->string('no_telephone');
            
            // Data Pemeriksaan
            $table->date('tanggal');
            $table->string('kilometer'); // Changed from bigInteger to string for flexibility
            $table->string('jam');
            $table->enum('fuel', ['E', '1/8', '1/4', '1/2', '3/4', 'F']);
            $table->string('pemeriksa');
            $table->enum('status', ['status', 'leasing', 'gs', 'replace', 'expedisi'])->nullable();
            
            // Data Kendaraan
            $table->unsignedBigInteger('kendaraan_id');
            $table->string('pemakaian_bbm')->nullable();
            $table->string('ratio')->nullable();
            
            // Foto-foto Kendaraan
            $table->string('foto_depan')->nullable();
            $table->string('foto_atas')->nullable();
            $table->string('foto_dalam')->nullable();
            $table->string('foto_belakang')->nullable();
            $table->string('foto_sampingkiri')->nullable();
            $table->string('foto_sampingkanan')->nullable();
            
            // Keterangan Foto
            $table->text('ket_depan')->nullable();
            $table->text('ket_atas')->nullable();
            $table->text('ket_dalam')->nullable();
            $table->text('ket_belakang')->nullable();
            $table->text('ket_sampingkiri')->nullable();
            $table->text('ket_sampingkanan')->nullable();
            $table->text('accecoris_tambahan')->nullable();
            
            // Kondisi Mesin & Komponen Utama (B = Baik, R = Rusak, NULL = Tidak Dicek)
            $table->enum('kondisi_mesin', ['B', 'R'])->nullable();
            $table->enum('kondisi_rem', ['B', 'R'])->nullable();
            $table->enum('kondisi_kopling', ['B', 'R'])->nullable();
            $table->enum('kondisi_ban', ['B', 'R'])->nullable();
            $table->enum('kondisi_ac', ['B', 'R'])->nullable();
            
            // Lampu-lampu
            $table->enum('lampu_lampusen', ['B', 'R'])->nullable();
            $table->enum('lampu_lampubesarkecil', ['B', 'R'])->nullable();
            $table->enum('lampu_rembikplat', ['B', 'R'])->nullable();
            
            // Komponen Cairan & Kebersihan
            $table->enum('accu', ['B', 'R'])->nullable();
            $table->enum('airradiator', ['B', 'R'])->nullable();
            $table->enum('olimesin_remkopling', ['B', 'R'])->nullable();
            $table->enum('olipower_steering', ['B', 'R'])->nullable();
            $table->enum('kebersihan_interior', ['B', 'R'])->nullable();
            $table->enum('kerapihanbody_cat', ['B', 'R'])->nullable();
            
            // Perlengkapan Kendaraan
            $table->enum('perlengkapan', ['B', 'R'])->nullable();
            $table->enum('ban_cadang', ['B', 'R'])->nullable();
            $table->enum('dongkrak', ['B', 'R'])->nullable();
            $table->enum('kunciroda', ['B', 'R'])->nullable();
            $table->enum('radio_tape', ['B', 'R'])->nullable();
            $table->enum('merk', ['B', 'R'])->nullable();
            $table->enum('karpet_lantai', ['B', 'R'])->nullable();
            $table->enum('doproda', ['B', 'R'])->nullable();
            $table->enum('pematikapi', ['B', 'R'])->nullable();
            $table->enum('segitiga_pengaman', ['B', 'R'])->nullable();
            $table->enum('kotakobat', ['B', 'R'])->nullable();
            $table->enum('stnk', ['B', 'R'])->nullable();
            $table->enum('kuncipintu', ['B', 'R'])->nullable();
            $table->enum('spedoometer', ['B', 'R'])->nullable();
            
            // Penanggung Jawab
            $table->string('sopir')->nullable();
            $table->string('mekanis')->nullable();
            $table->string('ttd_supir')->nullable(); // Signature image path
            $table->string('ttd_mekanik')->nullable(); // Signature image path
            $table->text('keterangan')->nullable();
            
            $table->timestamps();
            
            // Foreign Key
            $table->foreign('kendaraan_id')->references('id')->on('kendaraan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ceklist_kondisi');
    }
};
