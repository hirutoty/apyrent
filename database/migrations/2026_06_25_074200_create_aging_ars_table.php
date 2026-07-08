<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aging_ars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('member')->onDelete('cascade');
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->date('jatuh_tempo');
            $table->bigInteger('total');
            $table->string('kategori')->nullable();
            $table->string('bukti')->nullable();
            $table->enum('status', ['Belum Bayar', 'Bayar'])->default('Belum Bayar');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aging_ars');
    }

    
};
