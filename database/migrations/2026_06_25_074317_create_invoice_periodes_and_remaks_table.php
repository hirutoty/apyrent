<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Periode sewa per invoice (bisa lebih dari 1 periode per invoice)
        Schema::create('invoice_periodes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->foreign('invoice_id')
                  ->references('id')->on('invoices')
                  ->onDelete('cascade');

            $table->date('periode_awal')->nullable();
            $table->date('periode_akhir')->nullable();
            $table->timestamps();
        });

        // Rincian item / remaks per periode
        Schema::create('invoice_remaks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->foreign('invoice_id')
                  ->references('id')->on('invoices')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('periode_id')->nullable();
            $table->foreign('periode_id')
                  ->references('id')->on('invoice_periodes')
                  ->onDelete('cascade');

            $table->text('remaks')->nullable();          // nama / keterangan item
            $table->unsignedInteger('qty')->default(1)->nullable();
            $table->decimal('price', 15, 2)->default(0)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_remaks');
        Schema::dropIfExists('invoice_periodes');
    }
};