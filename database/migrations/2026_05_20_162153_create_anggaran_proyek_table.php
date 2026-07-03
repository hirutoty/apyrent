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
        Schema::create('anggaran_proyek', function (Blueprint $table) {
            $table->id();

            $table->string('proyek');
            $table->string('kategori');

            $table->decimal('budget', 15, 2);
            $table->decimal('realisasi', 15, 2);

            $table->decimal('sisa', 15, 2)->default(0);
            $table->decimal('persen_terpakai', 5, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggaran_proyek');
    }
};