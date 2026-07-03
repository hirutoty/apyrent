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
        Schema::create('gps', function (Blueprint   $table) {
    $table->id();

    $table->foreignId('user_id')
        ->constrained('users')
        ->cascadeOnDelete();

    $table->string('nama_gps');
    $table->text('alamat')->nullable();

    $table->string('nama_marketing')->nullable();
    $table->string('kontak_marketing')->nullable();

    $table->string('nama_bengkel')->nullable();
    $table->string('kontak_bengkel')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gps');
    }
};
