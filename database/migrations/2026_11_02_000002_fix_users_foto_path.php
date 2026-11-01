<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Fix foto path dari 'uploads/user/' menjadi 'user/'
     * untuk kompatibilitas dengan Storage::disk('public')
     */
    public function up(): void
    {
        // Update semua path foto yang masih menggunakan 'uploads/user/'
        DB::table('users')
            ->where('foto', 'like', 'uploads/user/%')
            ->update([
                'foto' => DB::raw("REPLACE(foto, 'uploads/user/', 'user/')")
            ]);
        
        // Log hasil update
        $updatedCount = DB::table('users')
            ->where('foto', 'like', 'user/%')
            ->count();
        
        echo "\n✅ Updated {$updatedCount} user foto paths from 'uploads/user/' to 'user/'\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback: kembalikan ke format lama
        DB::table('users')
            ->where('foto', 'like', 'user/%')
            ->whereNotNull('foto')
            ->update([
                'foto' => DB::raw("CONCAT('uploads/', foto)")
            ]);
    }
};
