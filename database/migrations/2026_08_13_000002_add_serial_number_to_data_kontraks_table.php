<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_kontraks', function (Blueprint $table) {
            // Serial number auto-generate (KTR-YYYYMM-XXXX), no_kontrak jadi input manual
            $table->string('serial_number')->nullable()->unique()->after('id');

            // no_kontrak sudah ada, ubah jadi nullable agar bisa diisi manual
            $table->string('no_kontrak')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('data_kontraks', function (Blueprint $table) {
            $table->dropColumn('serial_number');
            $table->string('no_kontrak')->nullable(false)->change();
        });
    }
};
