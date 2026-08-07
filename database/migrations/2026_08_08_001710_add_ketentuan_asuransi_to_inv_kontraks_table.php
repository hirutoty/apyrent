<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inv_kontraks', function (Blueprint $table) {
            // Kolom JSON untuk menyimpan ketentuan asuransi Pasal 4 ayat 7
            // Format: [{"id": "teks Indonesia", "en": "English text"}, ...]
            $table->json('ketentuan_asuransi')->nullable()->after('file_draft');
        });
    }

    public function down(): void
    {
        Schema::table('inv_kontraks', function (Blueprint $table) {
            $table->dropColumn('ketentuan_asuransi');
        });
    }
};
