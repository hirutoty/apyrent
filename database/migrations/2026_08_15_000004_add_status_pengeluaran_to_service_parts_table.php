<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_parts', function (Blueprint $table) {
            $table->enum('status_pengeluaran', ['stabil', 'overservice'])
                ->default('stabil')
                ->after('biaya')
                ->comment('stabil = biaya dalam batas limit kategori; overservice = biaya melebihi limit_price kategori kendaraan');
        });
    }

    public function down(): void
    {
        Schema::table('service_parts', function (Blueprint $table) {
            $table->dropColumn('status_pengeluaran');
        });
    }
};
