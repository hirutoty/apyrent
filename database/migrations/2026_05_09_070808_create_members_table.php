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

    $table->string('nama_member')->nullable();
    $table->string('kontak_member')->nullable();
    $table->string('email_member')->nullable();
    $table->enum('jenis_member', ['perorangan', 'perusahaan'])->nullable();
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
