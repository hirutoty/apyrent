<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();

            // relasi fleksibel (pajak, rental, kendaraan, dll)
            $table->string('relation_type'); // contoh: 'pajak', 'rental'
            $table->unsignedBigInteger('relation_id');

            // data file
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type')->nullable(); // pdf, jpg, png, dll
            $table->bigInteger('file_size')->nullable();

            $table->timestamps();

            // index biar cepat query
            $table->index(['relation_type', 'relation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};