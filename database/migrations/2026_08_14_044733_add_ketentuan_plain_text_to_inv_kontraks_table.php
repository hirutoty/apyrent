<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inv_kontraks', function (Blueprint $table) {
            $table->longText('ketentuan_id')->nullable()->after('pasal_ketentuan');
            $table->longText('ketentuan_en')->nullable()->after('ketentuan_id');
        });
    }

    public function down(): void
    {
        Schema::table('inv_kontraks', function (Blueprint $table) {
            $table->dropColumn(['ketentuan_id', 'ketentuan_en']);
        });
    }
};
