<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_kontraks', function (Blueprint $table) {
            // Hapus kolom lama
            $table->dropColumn('asuransi_leasing');

            // Tambah 6 kolom asuransi inline (semua nullable)
            $table->string('nama_asuransi')->nullable()->after('cara_bayar');
            $table->text('alamat_asuransi')->nullable()->after('nama_asuransi');
            $table->string('nama_marketing')->nullable()->after('alamat_asuransi');
            $table->string('kontak_marketing')->nullable()->after('nama_marketing');
            $table->string('nama_bengkel')->nullable()->after('kontak_marketing');
            $table->string('kontak_bengkel')->nullable()->after('nama_bengkel');
        });
    }

    public function down(): void
    {
        Schema::table('data_kontraks', function (Blueprint $table) {
            $table->dropColumn([
                'nama_asuransi',
                'alamat_asuransi',
                'nama_marketing',
                'kontak_marketing',
                'nama_bengkel',
                'kontak_bengkel',
            ]);
            $table->string('asuransi_leasing')->nullable()->after('cara_bayar');
        });
    }
};
