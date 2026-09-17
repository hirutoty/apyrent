<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_category_limits', function (Blueprint $table) {
            $table->integer('limit_km')
                ->nullable()
                ->after('limit_satuan')
                ->comment('Batas KM per penggantian part dalam kategori ini (opsional)');
        });
    }

    public function down(): void
    {
        Schema::table('service_category_limits', function (Blueprint $table) {
            $table->dropColumn('limit_km');
        });
    }
};
