<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inv_penawarans', function (Blueprint $table) {
            $table->json('ketentuan')->nullable()->after('file_penawaran');
        });
    }

    public function down(): void
    {
        Schema::table('inv_penawarans', function (Blueprint $table) {
            $table->dropColumn('ketentuan');
        });
    }
};
