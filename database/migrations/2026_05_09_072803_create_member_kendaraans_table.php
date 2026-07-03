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
       Schema::create('member_kendaraan', function (Blueprint $table) {
    $table->id();

    $table->foreignId('member_id')
        ->constrained('member')
        ->cascadeOnDelete();

    $table->foreignId('kendaraan_id')
        ->constrained('kendaraan')
        ->cascadeOnDelete();

    $table->date('tanggal_sewa');
    $table->date('tanggal_kembali');

    $table->bigInteger('biaya_sewa')->default(0);

    $table->enum('status_sewa', [
        'aktif',
        'selesai'
    ])->default('aktif');

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_kendaraan');
    }
};
