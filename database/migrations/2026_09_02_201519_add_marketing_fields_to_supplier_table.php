<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supplier', function (Blueprint $table) {
            $table->string('alamat')->nullable()->after('no_telp');
            $table->string('nama_marketing')->nullable()->after('alamat');
            $table->string('kontak_marketing')->nullable()->after('nama_marketing');
        });
    }

    public function down(): void
    {
        Schema::table('supplier', function (Blueprint $table) {
            $table->dropColumn(['alamat', 'nama_marketing', 'kontak_marketing']);
        });
    }
};
