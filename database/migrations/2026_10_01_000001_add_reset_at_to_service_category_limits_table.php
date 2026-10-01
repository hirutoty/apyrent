<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_category_limits', function (Blueprint $table) {
            // Tanggal reset limit — jika diisi, dipakai sebagai anchor awal periode baru
            // menggantikan tgl_pasang part pertama di DB. Null = belum pernah direset.
            $table->timestamp('reset_at')->nullable()->after('jumlah');
        });
    }

    public function down(): void
    {
        Schema::table('service_category_limits', function (Blueprint $table) {
            $table->dropColumn('reset_at');
        });
    }
};
