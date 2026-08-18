<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_parts', function (Blueprint $table) {
            $table->boolean('is_request')
                ->default(false)
                ->after('status_pengeluaran')
                ->comment('true = part ini dari Request Part, butuh approval per part');

            $table->enum('status_approval', ['pending', 'approved', 'rejected'])
                ->nullable()
                ->after('is_request')
                ->comment('null = bukan request, pending/approved/rejected = status approval per part');

            $table->unsignedBigInteger('approval_by')
                ->nullable()
                ->after('status_approval');

            $table->timestamp('approval_at')
                ->nullable()
                ->after('approval_by');

            $table->foreign('approval_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('service_parts', function (Blueprint $table) {
            $table->dropForeign(['approval_by']);
            $table->dropColumn(['is_request', 'status_approval', 'approval_by', 'approval_at']);
        });
    }
};
