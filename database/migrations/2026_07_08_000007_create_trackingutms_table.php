<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trackingutms', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tracking')->unique();
            $table->string('url_tujuan');
            $table->string('utm_source');
            $table->string('utm_medium');
            $table->string('utm_campaign');
            $table->string('utm_term')->nullable();
            $table->string('utm_content')->nullable();
            $table->integer('total_klik')->default(0);
            $table->integer('total_konversi')->default(0);
            $table->string('status')->default('Aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trackingutms');
    }
};
