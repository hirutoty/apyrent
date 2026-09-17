<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_asuransi_kejadians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_asuransi_id')
                  ->constrained('service_asuransi')
                  ->cascadeOnDelete();
            $table->string('nama_kejadian');
            $table->integer('biaya')->default(0);
            $table->json('lampiran')->nullable(); // [{path, name}]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_asuransi_kejadians');
    }
};
