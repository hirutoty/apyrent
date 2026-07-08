<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_prospeks', function (Blueprint $table) {
            $table->id();
            $table->string('kode_prospek')->unique();
            $table->string('nama_kontak');
            $table->string('perusahaan');
            $table->string('email')->nullable();
            $table->string('telepon')->nullable();
            $table->string('tahapan');
            $table->decimal('estimasi_deal', 15, 2)->nullable();
            $table->string('status');
            $table->string('sales');
            $table->date('tanggal_masuk');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_prospeks');
    }
};
