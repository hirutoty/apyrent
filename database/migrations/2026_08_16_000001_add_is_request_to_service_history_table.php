<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_history', function (Blueprint $table) {
            $table->boolean('is_request')
                ->default(false)
                ->after('status_approval')
                ->comment('true = dibuat via Request Part (butuh approval), false = tambah service langsung');
        });
    }

    public function down(): void
    {
        Schema::table('service_history', function (Blueprint $table) {
            $table->dropColumn('is_request');
        });
    }
};
