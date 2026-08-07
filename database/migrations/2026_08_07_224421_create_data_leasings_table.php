<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_leasings', function (Blueprint $table) {
            $table->id();

            // Relasi ke kontrak (optional, data bisa diisi manual juga)
            $table->unsignedBigInteger('kontrak_id')->nullable();
            $table->foreign('kontrak_id')
                  ->references('id')->on('inv_kontraks')
                  ->onDelete('set null');

            // Auto-fill dari kontrak
            $table->string('no_kontrak')->nullable();
            $table->string('mobil')->nullable();
            $table->string('tahun')->nullable();
            $table->string('nopol')->nullable();
            $table->string('user_leasing')->nullable(); // dari field kepada di penawaran

            // Input manual
            $table->bigInteger('angsuran_per_bulan')->default(0);
            $table->date('jatuh_tempo')->nullable();
            $table->string('periode')->nullable();
            $table->string('personal_account')->nullable();
            $table->string('sumber_dana_debit')->nullable();
            $table->string('cara_bayar')->nullable();
            $table->string('asuransi_leasing')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_leasings');
    }
};
