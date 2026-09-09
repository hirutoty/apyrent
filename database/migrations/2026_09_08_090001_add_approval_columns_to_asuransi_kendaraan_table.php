<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asuransi_kendaraan', function (Blueprint $table) {
            $table->unsignedBigInteger('pembayaran_id')->nullable()->after('tanggal_bayar');
            $table->string('persetujuan')->nullable()->after('pembayaran_id');
        });
    }

    public function down(): void
    {
        Schema::table('asuransi_kendaraan', function (Blueprint $table) {
            $table->dropColumn(['pembayaran_id', 'persetujuan']);
        });
    }
};
