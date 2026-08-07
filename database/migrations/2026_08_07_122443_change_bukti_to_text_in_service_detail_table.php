<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Migrasi data lama (string) ke format JSON array
        DB::table('service_detail')
            ->whereNotNull('bukti')
            ->where('bukti', '!=', '')
            ->get()
            ->each(function ($row) {
                $decoded = json_decode($row->bukti, true);
                // Jika belum JSON array, bungkus jadi array
                if (!is_array($decoded)) {
                    DB::table('service_detail')
                        ->where('id', $row->id)
                        ->update(['bukti' => json_encode([$row->bukti])]);
                }
            });

        Schema::table('service_detail', function (Blueprint $table) {
            $table->text('bukti')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('service_detail', function (Blueprint $table) {
            $table->string('bukti', 255)->nullable()->change();
        });
    }
};
