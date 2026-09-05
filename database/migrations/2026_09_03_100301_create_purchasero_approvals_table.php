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
        Schema::create('pembayaran_approvals', function (Blueprint $table) {
            $table->id();
            
            // Foreign key ke pembayarans
            $table->foreignId('pembayaran_id')
                ->constrained('pembayarans')
                ->onDelete('cascade')
                ->comment('Reference to pembayarans table');
            
            // User yang melakukan approval/rejection
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('User who performed the action');
            
            // Action type: approved atau rejected
            $table->enum('action', ['approved', 'rejected'])
                ->comment('Approval action: approved or rejected');
            
            // Catatan dari approver (wajib untuk reject, opsional untuk approve)
            $table->text('catatan')->nullable()
                ->comment('Notes from approver');
            
            // Bukti files yang diupload saat approval (JSON array)
            // Format: [{"original_name": "...", "stored_name": "...", "path": "...", "size": ...}]
            $table->json('bukti_files')->nullable()
                ->comment('Array of uploaded bukti files');
            
            // Attachment files yang diupload saat approval (JSON array, opsional)
            $table->json('attachment_files')->nullable()
                ->comment('Array of uploaded attachment files (optional)');
            
            $table->timestamps();
            
            // Indexes untuk performa
            $table->index('pembayaran_id', 'idx_approvals_pembayaran');
            $table->index('user_id', 'idx_approvals_user');
            $table->index('action', 'idx_approvals_action');
            $table->index('created_at', 'idx_approvals_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_approvals');
    }
};
