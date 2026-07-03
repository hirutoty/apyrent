<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bukti pembayaran per invoice (bisa cicilan / partial)
        Schema::create('invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->foreign('invoice_id')
                  ->references('id')->on('invoices')
                  ->onDelete('cascade');

            $table->decimal('amount', 15, 2)->nullable();
            $table->date('payment_date')->nullable();
            $table->text('method')->nullable();          // transfer, VA, tunai, dsb.
            $table->text('transaction_id')->nullable();
            $table->text('file_pembayaran')->nullable(); // path bukti transfer
            $table->text('status')->nullable();          // pending | verified
            $table->timestamps();
        });

        // Rekapitulasi per transaksi (penawaran → kontrak → invoice)
        Schema::create('inv_summaries', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('penawaran_id')->nullable();
            $table->foreign('penawaran_id')
                  ->references('id')->on('inv_penawarans')
                  ->onDelete('set null');

            $table->unsignedBigInteger('kontrak_id')->nullable();
            $table->foreign('kontrak_id')
                  ->references('id')->on('inv_kontraks')
                  ->onDelete('set null');

            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->foreign('invoice_id')
                  ->references('id')->on('invoices')
                  ->onDelete('set null');

            $table->text('type')->nullable();            // perorangan | perusahaan
            $table->decimal('total_amount', 15, 2)->default(0)->nullable();
            $table->decimal('paid_amount', 15, 2)->default(0)->nullable();
            $table->decimal('remaining_amount', 15, 2)->default(0)->nullable();
            $table->text('payment_status')->nullable();  // partial | lunas
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inv_summaries');
        Schema::dropIfExists('invoice_payments');
    }
};