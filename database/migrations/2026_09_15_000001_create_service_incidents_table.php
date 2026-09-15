<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kendaraan_id')->constrained('kendaraan')->cascadeOnDelete();
            $table->text('keluhan')->nullable();
            $table->unsignedInteger('kilometer')->default(0);
            $table->unsignedBigInteger('total_biaya')->default(0);
            $table->enum('status', ['proses', 'selesai'])->default('proses');
            $table->date('tanggal_service');
            $table->string('bukti_pembayaran')->nullable();
            $table->enum('status_approval', ['pending', 'approved', 'rejected'])->default('approved');
            $table->foreignId('approval_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approval_at')->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained('supplier')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_incidents');
    }
};
