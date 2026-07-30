<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inv_penawarans', function (Blueprint $table) {
            $table->text('email_person')->nullable()->after('contact_person');
            $table->text('alamat')->nullable()->after('email_person');
            $table->text('jenis_pelanggan')->nullable()->after('alamat');
        });
    }

    public function down(): void
    {
        Schema::table('inv_penawarans', function (Blueprint $table) {
            $table->dropColumn(['email_person', 'alamat', 'jenis_pelanggan']);
        });
    }
};
