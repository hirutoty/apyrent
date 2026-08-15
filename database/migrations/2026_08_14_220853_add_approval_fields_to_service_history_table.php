<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_history', function (Blueprint $table) {
            $table->enum('status_approval', ['pending', 'approved', 'rejected'])->nullable()->after('status_pengeluaran');
            $table->unsignedBigInteger('approval_by')->nullable()->after('status_approval');
            $table->timestamp('approval_at')->nullable()->after('approval_by');
            
            $table->foreign('approval_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('service_history', function (Blueprint $table) {
            $table->dropForeign(['approval_by']);
            $table->dropColumn(['status_approval', 'approval_by', 'approval_at']);
        });
    }
};
