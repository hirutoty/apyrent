<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_leasings', function (Blueprint $table) {
            // Ubah jatuh_tempo dari date ke integer (angka tanggal, misal: 7)
            $table->dropColumn('jatuh_tempo');
            $table->unsignedTinyInteger('jatuh_tempo')->nullable()->after('angsuran_per_bulan');

            // Ubah periode dari string menjadi dua kolom tanggal
            $table->dropColumn('periode');
            $table->date('periode_mulai')->nullable()->after('jatuh_tempo');
            $table->date('periode_selesai')->nullable()->after('periode_mulai');
        });
    }

    public function down(): void
    {
        Schema::table('data_leasings', function (Blueprint $table) {
            $table->dropColumn('jatuh_tempo');
            $table->date('jatuh_tempo')->nullable()->after('angsuran_per_bulan');

            $table->dropColumn(['periode_mulai', 'periode_selesai']);
            $table->string('periode')->nullable()->after('jatuh_tempo');
        });
    }
};
