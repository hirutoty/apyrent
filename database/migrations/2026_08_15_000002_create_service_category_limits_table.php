<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_category_limits', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kendaraan_id')
                ->constrained('kendaraan')
                ->cascadeOnDelete();

            $table->foreignId('category_id')
                ->constrained('service_categories')
                ->cascadeOnDelete();

            // Limit interval (berapa kali / berapa lama sebelum perlu diganti)
            $table->integer('limit_nilai')->default(1);
            $table->enum('limit_satuan', ['hari', 'minggu', 'bulan', 'tahun'])->default('tahun');

            // Batas harga per penggantian part dalam kategori ini
            $table->bigInteger('limit_price')->nullable()->comment('Batas harga maksimal biaya part (Rp)');

            $table->timestamps();

            // Satu kendaraan hanya boleh punya satu aturan per kategori
            $table->unique(['kendaraan_id', 'category_id'], 'uniq_kendaraan_category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_category_limits');
    }
};
