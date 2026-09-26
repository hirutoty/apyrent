<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_category_limits', function (Blueprint $table) {
            // Jumlah maksimal part aktif dalam kategori ini untuk satu kendaraan.
            // null = tidak dibatasi.
            $table->unsignedSmallInteger('jumlah')
                ->nullable()
                ->after('limit_km_interval')
                ->comment('Batas jumlah part aktif per kategori per kendaraan. null = tidak dibatasi.');
        });
    }

    public function down(): void
    {
        Schema::table('service_category_limits', function (Blueprint $table) {
            $table->dropColumn('jumlah');
        });
    }
};
