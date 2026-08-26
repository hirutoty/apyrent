<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inv_kontraks', function (Blueprint $table) {
            $table->string('perwakilan_pihak_kedua', 255)->nullable()->after('contact_kedua');
            $table->string('jabatan_pihak_kedua', 100)->nullable()->after('perwakilan_pihak_kedua');
        });
    }

    public function down(): void
    {
        Schema::table('inv_kontraks', function (Blueprint $table) {
            $table->dropColumn(['perwakilan_pihak_kedua', 'jabatan_pihak_kedua']);
        });
    }
};
