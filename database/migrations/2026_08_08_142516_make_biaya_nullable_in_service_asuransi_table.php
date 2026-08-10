<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_asuransi', function (Blueprint $table) {
            $table->decimal('biaya', 15, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('service_asuransi', function (Blueprint $table) {
            $table->decimal('biaya', 15, 2)->nullable(false)->change();
        });
    }
};
