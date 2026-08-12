<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_asuransi', function (Blueprint $table) {
            $table->string('nama_asuransi')->nullable()->after('kendaraan_id');
            $table->unsignedBigInteger('jenis_asuransi_id')->nullable()->after('nama_asuransi');
            
            $table->foreign('jenis_asuransi_id')
                  ->references('id')
                  ->on('jenis_asuransi')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('service_asuransi', function (Blueprint $table) {
            $table->dropForeign(['jenis_asuransi_id']);
            $table->dropColumn(['nama_asuransi', 'jenis_asuransi_id']);
        });
    }
};
