<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah kolom status_kendaraan dari enum ke string agar lebih fleksibel
        // sehingga mendukung nilai: aktif, expired, tidak_aktif
        DB::statement("ALTER TABLE asuransi_kendaraan MODIFY COLUMN status_kendaraan VARCHAR(20) NOT NULL DEFAULT 'aktif'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE asuransi_kendaraan MODIFY COLUMN status_kendaraan ENUM('aktif','expired') NOT NULL DEFAULT 'aktif'");
    }
};
