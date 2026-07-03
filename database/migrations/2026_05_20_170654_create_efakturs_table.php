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
        Schema::create('efakturs', function (Blueprint $table) {

            $table->id();

            $table->string('nomor_faktur')->nullable()->index();

            $table->date('tanggal_faktur')->nullable();

            $table->enum('tipe', [
                'Keluaran',
                'Masukan'
            ])->nullable();

            $table->string('npwp_lawan')->nullable();

            $table->string('nama_lawan')->nullable();

            $table->decimal('dpp', 20, 2)->nullable();

            $table->decimal('ppn', 20, 2)->nullable();

            $table->decimal('ppnbm', 20, 2)
                ->default(0);

            $table->string('status')
                ->default('Draft')
                ->nullable();

            $table->string('file_faktur')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('efakturs');
    }
};