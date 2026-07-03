<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inv_kontraks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('penawaran_id')->nullable();
            $table->foreign('penawaran_id')
                  ->references('id')->on('inv_penawarans')
                  ->onDelete('set null');

            $table->text('no_kontrak')->nullable();
            $table->date('tanggal_kontrak')->nullable();
            $table->date('perjanjian_pembayaran')->nullable();  // jatuh tempo pembayaran

            // Pihak pertama (perusahaan/pemilik)
            $table->text('pihak_pertama')->nullable();
            $table->text('contact_pertama')->nullable();

            // Pihak kedua (customer)
            $table->text('pihak_kedua')->nullable();
            $table->text('contact_kedua')->nullable();

            $table->text('file_kontrak')->nullable();
            $table->text('file_persyaratan')->nullable();

            $table->enum('status', [
                'dibuat', 'pending', 'approved',
                'active', 'rejected', 'expired',
                'completed', 'terminated',
            ])->default('pending')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inv_kontraks');
    }
};