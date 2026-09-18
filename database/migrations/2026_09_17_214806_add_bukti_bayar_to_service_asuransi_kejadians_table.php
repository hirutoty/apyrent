<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_asuransi_kejadians', function (Blueprint $table) {
            $table->json('bukti_bayar')->nullable()->after('lampiran');
        });
    }

    public function down(): void
    {
        Schema::table('service_asuransi_kejadians', function (Blueprint $table) {
            $table->dropColumn('bukti_bayar');
        });
    }
};
