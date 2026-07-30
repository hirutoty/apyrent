<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah no_ktp ke tabel member
        Schema::table('member', function (Blueprint $table) {
            $table->string('no_ktp', 20)->nullable()->after('kontak_pelanggan');
        });

        // 2. Tambah no_ktp ke tabel inv_penawarans
        Schema::table('inv_penawarans', function (Blueprint $table) {
            $table->string('no_ktp', 20)->nullable()->after('alamat');
        });

        // 3. Tambah fax ke tabel settings
        Schema::table('settings', function (Blueprint $table) {
            $table->string('fax')->nullable()->after('telepon');
        });
    }

    public function down(): void
    {
        Schema::table('member', function (Blueprint $table) {
            $table->dropColumn('no_ktp');
        });

        Schema::table('inv_penawarans', function (Blueprint $table) {
            $table->dropColumn('no_ktp');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('fax');
        });
    }
};
