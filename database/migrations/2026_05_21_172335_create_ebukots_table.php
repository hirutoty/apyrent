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
        Schema::create('bupot', function (Blueprint $table) {
    $table->id();

    $table->string('nomor_bukti')->nullable()->index();
    $table->date('tanggal_bukti')->nullable();

    $table->enum('tipe', [
        'PPh21',
        'PPh22',
        'PPh23',
        'PPh26'
    ])->nullable();

    $table->string('npwp_pemotong')->nullable();
    $table->string('nama_pemotong')->nullable();

    $table->string('npwp_dipotong')->nullable();
    $table->string('nama_dipotong')->nullable();

    $table->decimal('jumlah_bruto', 20, 2)->nullable();
    $table->decimal('tarif_pajak', 5, 2)->nullable();
    $table->decimal('jumlah_potong', 20, 2)->nullable();

    $table->enum('status', ['Draft', 'Approve', 'Submit DJP'])
          ->default('Draft');

    $table->string('file_bupot')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ebukots');
    }
};
