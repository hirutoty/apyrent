<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gps_kendaraan', function (Blueprint $table) {
            $table->enum('persetujuan', ['Disetujui', 'Ditolak'])->nullable()->after('nama_pemilik');
        });
    }

    public function down(): void
    {
        Schema::table('gps_kendaraan', function (Blueprint $table) {
            $table->dropColumn('persetujuan');
        });
    }
};
