<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inv_penawarans', function (Blueprint $table) {
            $table->id();
            $table->text('no_penawaran')->nullable();
            $table->date('tanggal_penawaran')->nullable();
            $table->text('kepada')->nullable();
            $table->text('up')->nullable();
            $table->text('perihal')->nullable();

            // Kontak
            $table->text('customer_name')->nullable();
            $table->text('contact_person')->nullable();

            // Pengirim & penandatangan
            $table->text('pengirim')->nullable();
            $table->unsignedInteger('periode')->nullable();
            $table->text('staff')->nullable();           // jabatan staf
            $table->text('name_staff')->nullable();
            $table->text('direktur')->nullable();        // jabatan direktur
            $table->text('name_direktur')->nullable();

            $table->enum('status', [
                'dibuat', 'pending', 'approved',
                'active', 'rejected', 'expired',
                'completed', 'terminated',
            ])->default('pending')->nullable();

            $table->text('total')->nullable();
            $table->text('file_penawaran')->nullable();
            $table->text('file_persyaratan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inv_penawarans');
    }
};