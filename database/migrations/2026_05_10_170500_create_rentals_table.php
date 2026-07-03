<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('kendaraan_id')
                ->constrained('kendaraan')
                ->cascadeOnDelete();

            $table->foreignId('member_id')
                ->constrained('member')
                ->cascadeOnDelete();

            $table->dateTime('tanggal_mulai');
            $table->dateTime('tanggal_selesai')->nullable();
            $table->string('tujuan')->nullable();

            $table->integer('durasi_jam')->nullable();
            $table->integer('durasi_hari')->nullable();
            $table->integer('durasi_bulan')->nullable();

            

            // biaya
            $table->bigInteger('biaya_dasar')->default(0);
            $table->bigInteger('biaya_tambahan_total')->default(0);
            $table->bigInteger('total_biaya')->default(0);

            // metode bayar
            $table->enum('metode_pembayaran', [
                'tunai',
                'transfer'
            ])->default('transfer');

            // lunas atau dp
            $table->enum('jenis_pembayaran', [
                'lunas',
                'dp'
            ])->default('lunas');

            // nominal dp
            $table->bigInteger('nominal_dp')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Bukti Pembayaran
            |--------------------------------------------------------------------------
            | LUNAS
            | - bukti_lunas
            |
            | DP
            | - bukti_dp
            | - bukti_pelunasan (diupload nanti)
            |--------------------------------------------------------------------------
            */
            $table->string('nama_driver')->nullable();
            $table->string('kontak_driver')->nullable();
            $table->bigInteger('biaya_driver')->nullable();

            $table->string('bukti_lunas')->nullable();
            $table->string('bukti_dp')->nullable();
            $table->string('bukti_pelunasan')->nullable();

            // status pembayaran
            $table->enum('status_pembayaran', [
                'belum_bayar',
                'dp',
                'lunas'
            ])->default('belum_bayar');

            // status rental
            $table->enum('status', [
                'Pending',
                'booking',
                'aktif',
                'selesai',
                'batal'
            ])->default('Pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};