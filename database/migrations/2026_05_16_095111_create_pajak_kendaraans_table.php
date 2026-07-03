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
        Schema::create('pajak_kendaraans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kendaraan_id')
                ->constrained('kendaraan')
                ->cascadeOnDelete();

            $table->string('jenis_pajak');
            // tahunan, 5 tahunan, kir, dll

            $table->decimal('nominal', 15, 2);

            $table->date('jatuh_tempo');

            $table->date('tanggal_bayar')->nullable();

            $table->enum('status', [
                'belum_bayar',
                'sudah_bayar'
            ])->default('belum_bayar');

            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pajak_kendaraans');
    }
};
