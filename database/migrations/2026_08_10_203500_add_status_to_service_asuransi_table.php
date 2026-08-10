<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_asuransi', function (Blueprint $table) {
            $table->enum('status', ['bermasalah', 'selesai'])->default('bermasalah')->after('attachment');
        });
    }

    public function down(): void
    {
        Schema::table('service_asuransi', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
