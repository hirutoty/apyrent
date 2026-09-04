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
        Schema::table('purchaseros', function (Blueprint $table) {
            // Check if columns don't exist before adding
            if (!Schema::hasColumn('purchaseros', 'nama_rekening')) {
                $table->string('nama_rekening', 255)->nullable()->after('no_rekening');
            }
            
            if (!Schema::hasColumn('purchaseros', 'informasi')) {
                $table->text('informasi')->nullable()->after('alasan_permintaan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchaseros', function (Blueprint $table) {
            // Only drop columns that were added by this migration
            if (Schema::hasColumn('purchaseros', 'nama_rekening')) {
                $table->dropColumn('nama_rekening');
            }
            if (Schema::hasColumn('purchaseros', 'informasi')) {
                $table->dropColumn('informasi');
            }
        });
    }
};
