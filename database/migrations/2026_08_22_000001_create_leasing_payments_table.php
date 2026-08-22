<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leasing_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('data_leasing_id');
            // Format: YYYY-MM-01 — identifikasi bulan cicilan, cegah double-catat
            $table->date('bulan_cicilan');
            $table->bigInteger('nominal');
            $table->date('tanggal_catat');
            // FK ke keuangans untuk trace balik
            $table->unsignedBigInteger('keuangan_id')->nullable();
            $table->timestamps();

            $table->foreign('data_leasing_id')
                  ->references('id')->on('data_leasings')
                  ->onDelete('cascade');

            // Satu cicilan per leasing per bulan — tidak bisa double-catat
            $table->unique(['data_leasing_id', 'bulan_cicilan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leasing_payments');
    }
};
