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
        Schema::create('bukubesars', function (Blueprint $table) {

            $table->id();

            $table->string('kode_jurnal')->nullable();

            $table->string('transaksi')->nullable();

            $table->enum('kategori', [
                'Pendapatan',
                'Beban',
                'Aktiva',
                'Modal',
                'Kewajiban',
            ])->nullable();

            $table->date('tanggal')->nullable();

            $table->bigInteger('debit')->default(0);

            $table->bigInteger('kredit')->default(0);

            $table->bigInteger('saldo')->default(0);

            $table->string('aktivitas')->nullable();

            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bukubesars');
    }
};