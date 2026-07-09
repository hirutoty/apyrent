<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kontak')->nullable();
            $table->string('email')->nullable();
            $table->enum('jenis_member', ['perorangan', 'perusahaan'])->default('perorangan');
            $table->text('alamat')->nullable();
            $table->string('file_stnk')->nullable();
            $table->string('file_attachment')->nullable();
            $table->string('file_kontrak')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
