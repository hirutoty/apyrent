<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('service_parts', function (Blueprint $table) {
            // Add replacement tracking fields
            $table->timestamp('replaced_at')->nullable()->after('biaya');
            $table->unsignedBigInteger('replaced_by_part_id')->nullable()->after('replaced_at');
            
            // Foreign key to track replacement part
            $table->foreign('replaced_by_part_id')
                ->references('id')
                ->on('service_parts')
                ->nullOnDelete();
        });

        // Update status enum to include 'Diganti'
        DB::statement("ALTER TABLE service_parts MODIFY COLUMN status ENUM('Terpasang', 'Limit', 'Diganti') DEFAULT 'Terpasang'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_parts', function (Blueprint $table) {
            $table->dropForeign(['replaced_by_part_id']);
            $table->dropColumn(['replaced_at', 'replaced_by_part_id']);
        });

        // Revert status enum
        DB::statement("ALTER TABLE service_parts MODIFY COLUMN status ENUM('Terpasang', 'Limit') DEFAULT 'Terpasang'");
    }
};
