<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('network_monitorings', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perangkat');
            $table->string('tipe_perangkat'); // Router / Switch / Access Point / Firewall / Server / Lainnya
            $table->string('ip_address')->nullable();
            $table->string('mac_address')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('vendor')->nullable();
            $table->string('model')->nullable();
            $table->date('tanggal_instalasi')->nullable();
            $table->string('uptime')->nullable();
            $table->string('bandwidth')->nullable();
            $table->string('status'); // Online / Offline / Warning / Maintenance
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('network_monitorings');
    }
};
