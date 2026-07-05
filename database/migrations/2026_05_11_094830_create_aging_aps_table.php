<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aging_aps', function (Blueprint $table) {
            $table->id();
            $table->string('vendor');
            $table->string('no_tagihan');
            $table->date('jatuh_tempo');
            $table->bigInteger('jumlah');
            $table->string('kategori');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aging_aps');
    }
};
