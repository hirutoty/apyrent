<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ads_integrations', function (Blueprint $table) {
            $table->id();
            $table->string('id_iklan')->unique();
            $table->string('nama_iklan');
            $table->string('platform');
            $table->date('tanggal_aktif');
            $table->decimal('budget_harian', 15, 2);
            $table->integer('klik')->default(0);
            $table->integer('konversi')->default(0);
            $table->decimal('biaya_total', 15, 2)->default(0);
            $table->decimal('penjualan', 15, 2)->default(0);
            $table->string('roi')->default('0%');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ads_integrations');
    }
};
