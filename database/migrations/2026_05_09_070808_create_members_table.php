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
        Schema::create('member', function (Blueprint $table) {
    $table->id();

    $table->string('nama_pelanggan')->nullable();
    $table->string('kontak_pelanggan')->nullable();
    $table->string('email_pelanggan')->nullable();
    $table->enum('jenis_pelanggan', ['perorangan', 'perusahaan'])->nullable();
    $table->text('alamat')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};

