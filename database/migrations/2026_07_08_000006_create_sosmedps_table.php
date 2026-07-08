<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sosmedps', function (Blueprint $table) {
            $table->id();
            $table->string('id_kampanye')->unique();
            $table->string('channel');
            $table->string('utm_source');
            $table->string('utm_campaign');
            $table->integer('klik')->default(0);
            $table->integer('konversi')->default(0);
            $table->decimal('total_biaya', 15, 2)->default(0);
            $table->decimal('total_penjualan', 15, 2)->default(0);
            $table->decimal('roi', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sosmedps');
    }
};
