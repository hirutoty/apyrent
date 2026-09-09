<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_parts', function (Blueprint $table) {
            $table->string('nama_rekening', 150)->nullable()->after('keterangan');
            $table->string('nama_bank', 100)->nullable()->after('nama_rekening');
            $table->string('no_rekening', 50)->nullable()->after('nama_bank');
        });
    }

    public function down(): void
    {
        Schema::table('service_parts', function (Blueprint $table) {
            $table->dropColumn(['nama_rekening', 'nama_bank', 'no_rekening']);
        });
    }
};
