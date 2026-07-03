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
        Schema::create('pajak_histories', function (Blueprint $table) {
    $table->id();

    $table->foreignId('pajak_kendaraan_id')
        ->constrained('pajak_kendaraans')
        ->cascadeOnDelete();

    $table->foreignId('kendaraan_id')
        ->constrained('kendaraan')
        ->cascadeOnDelete();

    $table->string('jenis_pajak');

    $table->decimal('nominal',15,2);

    $table->date('jatuh_tempo');

    $table->date('tanggal_bayar')->nullable();

    $table->string('status');

    $table->text('keterangan')->nullable();

    $table->string('bukti')->nullable();

    $table->timestamp('diperpanjang_pada');

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pajak_histories');
    }
};
