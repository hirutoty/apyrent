<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            // Tujuan perjalanan: Dalam Kota / Luar Kota
            $table->enum('tujuan_perjalanan', ['dalam_kota', 'luar_kota'])->nullable()->after('tujuan');
            // Pengantaran & penjemputan (opsional, untuk pelanggan yang pakai driver)
            $table->string('alamat_pengantaran')->nullable()->after('tujuan_perjalanan');
            $table->string('alamat_penjemputan')->nullable()->after('alamat_pengantaran');
        });
    }

    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropColumn(['tujuan_perjalanan', 'alamat_pengantaran', 'alamat_penjemputan']);
        });
    }
};
