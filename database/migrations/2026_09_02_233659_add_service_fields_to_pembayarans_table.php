<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            // Tipe pembayaran: belanja atau service (default belanja)
            $table->enum('tipe_pembayaran', ['belanja', 'service'])->default('belanja')->after('departemen');

            // Data khusus service kendaraan
            $table->foreignId('kendaraan_id')
                ->nullable()
                ->after('tipe_pembayaran')
                ->constrained('kendaraan')
                ->nullOnDelete();

            $table->date('tanggal_service')->nullable()->after('kendaraan_id');
            $table->bigInteger('kilometer')->nullable()->after('tanggal_service');
            $table->text('keluhan')->nullable()->after('kilometer');

            // File disimpan sebagai path string (diupload saat disetujui)
            $table->string('bukti_pembayaran')->nullable()->after('keluhan');
            $table->string('lampiran_tambahan')->nullable()->after('bukti_pembayaran');

            // Data pembayaran / penerima
            $table->string('nama_penerima')->nullable()->after('lampiran_tambahan');
            $table->string('nama_bank')->nullable()->after('nama_penerima');
            $table->string('no_rekening')->nullable()->after('nama_bank');
        });
    }

    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropForeign(['kendaraan_id']);
            $table->dropColumn([
                'tipe_pembayaran',
                'kendaraan_id',
                'tanggal_service',
                'kilometer',
                'keluhan',
                'bukti_pembayaran',
                'lampiran_tambahan',
                'nama_penerima',
                'nama_bank',
                'no_rekening',
            ]);
        });
    }
};
