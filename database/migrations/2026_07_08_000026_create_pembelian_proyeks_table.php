<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembelian_proyeks', function (Blueprint $table) {
            $table->id();
            $table->string('pr_no')->unique();
            $table->string('proyek');
            $table->string('item_diminta');
            $table->integer('qty')->default(0);
            $table->string('vendor')->nullable();
            $table->bigInteger('estimasi_harga')->default(0);
            $table->string('status');
            $table->date('tgl_permintaan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembelian_proyeks');
    }
};
