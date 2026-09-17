<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_incident_parts', function (Blueprint $table) {
            $table->renameColumn('keterangan', 'keterangan_limit');
        });
    }

    public function down(): void
    {
        Schema::table('service_incident_parts', function (Blueprint $table) {
            $table->renameColumn('keterangan_limit', 'keterangan');
        });
    }
};
