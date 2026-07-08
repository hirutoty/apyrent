<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_timelines', function (Blueprint $table) {
            $table->id();
            $table->string('proyek');
            $table->string('kegiatan');
            $table->date('deadline');
            $table->boolean('reminder')->default(false);
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_timelines');
    }
};
