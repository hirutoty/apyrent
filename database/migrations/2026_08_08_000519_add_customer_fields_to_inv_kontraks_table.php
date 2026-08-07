<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inv_kontraks', function (Blueprint $table) {
            $table->string('no_ktp_kedua', 16)->nullable()->after('contact_kedua');
            $table->string('email_kedua')->nullable()->after('no_ktp_kedua');
            $table->string('jenis_pelanggan')->nullable()->after('email_kedua');
        });
    }

    public function down(): void
    {
        Schema::table('inv_kontraks', function (Blueprint $table) {
            $table->dropColumn(['no_ktp_kedua', 'email_kedua', 'jenis_pelanggan']);
        });
    }
};
