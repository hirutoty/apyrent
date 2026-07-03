<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('penawaran_id')->nullable();
            $table->foreign('penawaran_id')
                ->references('id')
                ->on('inv_penawarans')
                ->onDelete('set null');

            $table->unsignedBigInteger('kontrak_id')->nullable();
            $table->foreign('kontrak_id')
                ->references('id')
                ->on('inv_kontraks')
                ->onDelete('set null');

            // Ambil kendaraan dari tabel kendaraans
            $table->unsignedBigInteger('kendaraan_id')->nullable();
            $table->foreign('kendaraan_id')
                ->references('id')
                ->on('kendaraan')
                ->onDelete('set null');

            $table->enum('type', ['perorangan', 'perusahaan'])->default('perorangan');

            $table->text('invoice_no')->nullable();
            $table->text('order_no')->nullable();

            $table->text('customer_name')->nullable();
            $table->text('customer_address')->nullable();
            $table->text('contact_person')->nullable();
            $table->text('telephone')->nullable();

            $table->text('satuan')->nullable();
            $table->date('invoice_date')->nullable();

          

            $table->text('pengirim')->nullable();
            $table->text('staff')->nullable();
            $table->text('name_staff')->nullable();
            $table->text('direktur')->nullable();
            $table->text('name_direktur')->nullable();

            $table->enum('status', [
                'draft',
                'partial',
                'overdue',
                'lunas'
            ])->nullable();

            $table->enum('payment_status', [
                'unpaid',
                'paid'
            ])->default('unpaid')->nullable();

            $table->decimal('ppn', 15, 2)->default(0);
            $table->decimal('pph', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
