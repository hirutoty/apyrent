<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('serahterima_kontrak', function (Blueprint $table) {
            // Drop old foreign key
            $table->dropForeign(['idkontrak']);
            
            // Add new foreign key to inv_kontraks
            $table->foreign('idkontrak')->references('id')->on('inv_kontraks')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('serahterima_kontrak', function (Blueprint $table) {
            $table->dropForeign(['idkontrak']);
            $table->foreign('idkontrak')->references('id')->on('data_kontraks')->onDelete('set null');
        });
    }
};
