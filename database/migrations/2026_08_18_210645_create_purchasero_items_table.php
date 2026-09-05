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
        Schema::create('pembayaran_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_id')->constrained('pembayarans')->onDelete('cascade');
            
            // Item details
            $table->string('nama_barang');
            $table->string('kategori')->nullable();
            $table->string('posisi')->nullable();
            $table->string('part_number')->nullable();
            $table->string('serial_number')->nullable();
            
            // Quantity & pricing
            $table->integer('qty');
            $table->string('satuan')->nullable();
            $table->decimal('harga_satuan', 15, 2)->nullable();
            $table->decimal('subtotal', 15, 2)->nullable();
            
            // Additional info
            $table->text('spesifikasi')->nullable();
            $table->string('merk')->nullable();
            $table->text('keterangan')->nullable();
            
            // Attachments (JSON array of file paths)
            $table->json('bukti')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_items');
    }
};
