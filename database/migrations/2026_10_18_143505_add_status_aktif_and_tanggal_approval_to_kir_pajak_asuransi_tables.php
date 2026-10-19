<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ===== TABEL KIR =====
        // Tambah field status_aktif dan tanggal_approval
        Schema::table('kir', function (Blueprint $table) {
            // Cek apakah kolom status_aktif sudah ada, jika belum baru tambahkan
            if (!Schema::hasColumn('kir', 'status_aktif')) {
                $table->enum('status_aktif', ['aktif', 'expired', 'tidak_aktif'])
                      ->default('tidak_aktif')
                      ->after('status')
                      ->comment('Status aktif: aktif/expired/tidak_aktif');
            }
            
            // Cek apakah kolom tanggal_approval sudah ada, jika belum baru tambahkan
            if (!Schema::hasColumn('kir', 'tanggal_approval')) {
                $table->dateTime('tanggal_approval')
                      ->nullable()
                      ->after('persetujuan')
                      ->comment('Tanggal saat pembayaran disetujui');
            }
        });

        // ===== TABEL PAJAK_KENDARAANS =====
        // Tambah field tanggal_approval (status_aktif sudah ada)
        Schema::table('pajak_kendaraans', function (Blueprint $table) {
            if (!Schema::hasColumn('pajak_kendaraans', 'tanggal_approval')) {
                $table->dateTime('tanggal_approval')
                      ->nullable()
                      ->after('persetujuan')
                      ->comment('Tanggal saat pembayaran disetujui');
            }
        });

        // ===== TABEL ASURANSI_KENDARAAN =====
        // Rename status_kendaraan menjadi status_aktif dan tambah tanggal_approval
        
        // Cek apakah masih ada kolom status_kendaraan yang perlu direname
        if (Schema::hasColumn('asuransi_kendaraan', 'status_kendaraan')) {
            // Step 1: Buat kolom status_aktif baru dengan ENUM yang sama
            DB::statement("
                ALTER TABLE asuransi_kendaraan 
                ADD COLUMN status_aktif ENUM('aktif', 'expired', 'tidak_aktif') 
                NOT NULL DEFAULT 'tidak_aktif' 
                COMMENT 'Status aktif: aktif/expired/tidak_aktif'
                AFTER tgl_berakhir
            ");

            // Step 2: Copy data dari status_kendaraan ke status_aktif
            DB::statement("
                UPDATE asuransi_kendaraan 
                SET status_aktif = status_kendaraan
            ");

            // Step 3: Drop kolom status_kendaraan yang lama
            Schema::table('asuransi_kendaraan', function (Blueprint $table) {
                $table->dropColumn('status_kendaraan');
            });
        }

        // Step 4: Tambah tanggal_approval jika belum ada
        Schema::table('asuransi_kendaraan', function (Blueprint $table) {
            if (!Schema::hasColumn('asuransi_kendaraan', 'tanggal_approval')) {
                $table->dateTime('tanggal_approval')
                      ->nullable()
                      ->after('persetujuan')
                      ->comment('Tanggal saat pembayaran disetujui');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ===== ROLLBACK TABEL KIR =====
        Schema::table('kir', function (Blueprint $table) {
            $table->dropColumn(['status_aktif', 'tanggal_approval']);
        });

        // ===== ROLLBACK TABEL PAJAK_KENDARAANS =====
        Schema::table('pajak_kendaraans', function (Blueprint $table) {
            $table->dropColumn('tanggal_approval');
        });

        // ===== ROLLBACK TABEL ASURANSI_KENDARAAN =====
        
        // Step 1: Buat kembali kolom status_kendaraan
        DB::statement("
            ALTER TABLE asuransi_kendaraan 
            ADD COLUMN status_kendaraan ENUM('aktif', 'expired', 'tidak_aktif') 
            NOT NULL DEFAULT 'tidak_aktif' 
            AFTER tgl_berakhir
        ");

        // Step 2: Copy data dari status_aktif kembali ke status_kendaraan
        DB::statement("
            UPDATE asuransi_kendaraan 
            SET status_kendaraan = status_aktif
        ");

        // Step 3: Drop status_aktif dan tanggal_approval
        Schema::table('asuransi_kendaraan', function (Blueprint $table) {
            $table->dropColumn(['status_aktif', 'tanggal_approval']);
        });
    }
};
