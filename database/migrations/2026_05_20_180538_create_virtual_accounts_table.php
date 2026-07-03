<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('virtual_accounts', function (Blueprint $table) {

            $table->id();

            $table->string('va_number')->unique();

            $table->foreignId('member_id')
                ->nullable()
                ->constrained('member')
                ->nullOnDelete();


            $table->string('invoice_id')->nullable();

            $table->string('bukti_pembayaran')->nullable();

            $table->string('bank')->nullable();

            $table->decimal('expected_amount', 15, 2)->default(0);

            $table->decimal('paid_amount', 15, 2)->default(0);

            $table->enum('status', [
                'Pending',
                'paid'
            ])->default('Pending');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtual_accounts');
    }
};