<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inv_kontraks', function (Blueprint $table) {
            $table->json('pasal_ketentuan')->nullable()->after('ketentuan_asuransi');
        });
    }

    public function down(): void
    {
        Schema::table('inv_kontraks', function (Blueprint $table) {
            $table->dropColumn('pasal_ketentuan');
        });
    }
};
