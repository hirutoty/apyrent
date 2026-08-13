<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->timestamps();
        });

        // Seed kategori default umum
        DB::table('service_categories')->insert([
            ['nama' => 'Mesin',       'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Kaki-kaki',   'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Elektrikal',  'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Ban',         'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Transmisi',   'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'AC',          'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Bodi',        'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Lainnya',     'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('service_categories');
    }
};
