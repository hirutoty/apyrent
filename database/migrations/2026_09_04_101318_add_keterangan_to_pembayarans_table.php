<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            // Keterangan singkat / slug otomatis dari service, atau diisi manual
            $table->string('keterangan', 500)->nullable()->after('alasan_permintaan');
        });
    }

    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
    }
};
