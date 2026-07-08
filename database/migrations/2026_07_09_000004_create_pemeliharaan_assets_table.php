<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemeliharaan_assets', function (Blueprint $table) {
            $table->id();
            $table->string('kode_aset');
            $table->date('tanggal_service');
            $table->string('jenis_service');
            $table->string('vendor_pic');
            $table->decimal('biaya', 15, 2);
            $table->string('status');
            $table->date('jadwal_selanjutnya')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemeliharaan_assets');
    }
};
