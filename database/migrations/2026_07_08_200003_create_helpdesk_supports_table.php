<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('helpdesk_supports', function (Blueprint $table) {
            $table->id();
            $table->string('no_tiket')->unique();
            $table->string('judul');
            $table->text('deskripsi');
            $table->string('kategori'); // Hardware / Software / Network / Lainnya
            $table->string('prioritas'); // Low / Medium / High / Critical
            $table->string('pemohon');
            $table->string('departemen')->nullable();
            $table->string('petugas')->nullable();
            $table->date('tanggal_masuk');
            $table->date('tanggal_selesai')->nullable();
            $table->string('status'); // Open / In Progress / Resolved / Closed
            $table->text('solusi')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('helpdesk_supports');
    }
};
