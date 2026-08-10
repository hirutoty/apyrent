<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_leasings', function (Blueprint $table) {
            // Hapus FK lama ke inv_kontraks
            $table->dropForeign(['kontrak_id']);
            $table->dropColumn('kontrak_id');

            // Tambah FK baru ke data_kontraks
            $table->unsignedBigInteger('data_kontrak_id')->nullable()->after('id');
            $table->foreign('data_kontrak_id')
                  ->references('id')->on('data_kontraks')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('data_leasings', function (Blueprint $table) {
            $table->dropForeign(['data_kontrak_id']);
            $table->dropColumn('data_kontrak_id');

            $table->unsignedBigInteger('kontrak_id')->nullable()->after('id');
            $table->foreign('kontrak_id')
                  ->references('id')->on('inv_kontraks')
                  ->onDelete('set null');
        });
    }
};
