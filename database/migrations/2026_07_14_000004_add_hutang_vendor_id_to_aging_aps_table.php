<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('aging_aps', function (Blueprint $table) {
            $table->unsignedBigInteger('hutang_vendor_id')->nullable()->after('kategori');
            $table->foreign('hutang_vendor_id')->references('id')->on('hutang_vendors')->onDelete('set null');
            $table->boolean('status_lunas')->default(false)->after('hutang_vendor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aging_aps', function (Blueprint $table) {
            $table->dropForeign(['hutang_vendor_id']);
            $table->dropColumn(['hutang_vendor_id', 'status_lunas']);
        });
    }
};
