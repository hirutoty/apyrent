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
        Schema::table('member', function (Blueprint $table) {
            $table->renameColumn('nama_member', 'nama_pelanggan');
            $table->renameColumn('kontak_member', 'kontak_pelanggan');
            $table->renameColumn('email_member', 'email_pelanggan');
            $table->renameColumn('jenis_member', 'jenis_pelanggan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('member', function (Blueprint $table) {
            $table->renameColumn('nama_pelanggan', 'nama_member');
            $table->renameColumn('kontak_pelanggan', 'kontak_member');
            $table->renameColumn('email_pelanggan', 'email_member');
            $table->renameColumn('jenis_pelanggan', 'jenis_member');
        });
    }
};
