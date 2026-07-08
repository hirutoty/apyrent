<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_pricelists', function (Blueprint $table) {
            $table->id();
            $table->string('vendor');
            $table->string('kode_barang');
            $table->string('nama_barang');
            $table->bigInteger('harga_per_unit');
            $table->string('satuan');
            $table->decimal('diskon', 5, 2)->default(0);
            $table->integer('minimal_order')->default(1);
            $table->integer('lead_time')->default(0)->comment('dalam hari');
            $table->date('tanggal_berlaku');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_pricelists');
    }
};
