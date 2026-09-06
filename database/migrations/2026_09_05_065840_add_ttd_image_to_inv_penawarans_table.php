<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inv_penawarans', function (Blueprint $table) {
            $table->string('ttd_image')->nullable()->after('name_staff');
        });
    }

    public function down(): void
    {
        Schema::table('inv_penawarans', function (Blueprint $table) {
            $table->dropColumn('ttd_image');
        });
    }
};
