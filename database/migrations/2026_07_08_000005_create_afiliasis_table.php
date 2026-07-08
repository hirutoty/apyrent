<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('afiliasis', function (Blueprint $table) {
            $table->id();
            $table->string('id_program')->unique();
            $table->string('nama_program');
            $table->string('kode_referral')->unique();
            $table->decimal('diskon_referral', 15, 2);
            $table->string('bonus_pengajak');
            $table->date('batas_waktu');
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('afiliasis');
    }
};
