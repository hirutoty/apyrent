<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_payments', function (Blueprint $table) {
            // Multiple attachments disimpan sebagai JSON array [{path, name, type}]
            $table->text('attachment')->nullable()->after('file_pembayaran_name');
        });
    }

    public function down(): void
    {
        Schema::table('invoice_payments', function (Blueprint $table) {
            $table->dropColumn('attachment');
        });
    }
};
