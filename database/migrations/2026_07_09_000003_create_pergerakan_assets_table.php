<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pergerakan_assets', function (Blueprint $table) {
            $table->id();
            $table->string('kode_aset');
            $table->date('tanggal');
            $table->string('jenis_pergerakan');
            $table->string('dari_lokasi');
            $table->string('ke_lokasi');
            $table->string('dilakukan_oleh');
            $table->string('disetujui_oleh');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pergerakan_assets');
    }
};
