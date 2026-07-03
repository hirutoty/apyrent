ko<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keuangans', function (Blueprint $table) {
            $table->id();

            $table->date('tanggal');

            $table->string('reference')->nullable(); 
            // contoh: RENTAL-1 / EXP-123

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('kategori');
            // contoh: Rental, Operasional, Servis, dll

            $table->string('metode')->nullable();
            // cash / transfer

            $table->text('keterangan')->nullable();

            $table->decimal('pemasukan', 15, 2)->default(0);
            $table->decimal('pengeluaran', 15, 2)->default(0);

            $table->decimal('saldo', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keuangans');
    }
};