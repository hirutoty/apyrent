<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otomatisasis', function (Blueprint $table) {
            $table->id();
            $table->string('workflow_id')->unique();
            $table->string('nama_workflow');
            $table->string('trigger_event');
            $table->string('syarat_tambahan')->nullable();
            $table->string('aksi');
            $table->string('delay_aksi')->nullable();
            $table->string('status')->default('Aktif');
            $table->string('pic')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otomatisasis');
    }
};
