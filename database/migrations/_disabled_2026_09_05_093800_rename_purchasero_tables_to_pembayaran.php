<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Rename purchasero tables → pembayaran tables
     * Rename kolom tipe_pengadaan → tipe_pembayaran
     */
    public function up(): void
    {
        // 1. Rename tabel utama: purchaseros → pembayarans
        if (Schema::hasTable('purchaseros') && !Schema::hasTable('pembayarans')) {
            Schema::rename('purchaseros', 'pembayarans');
        }

        // 2. Rename kolom tipe_pengadaan → tipe_pembayaran di tabel pembayarans
        if (Schema::hasTable('pembayarans') && Schema::hasColumn('pembayarans', 'tipe_pengadaan')) {
            Schema::table('pembayarans', function (Blueprint $table) {
                $table->renameColumn('tipe_pengadaan', 'tipe_pembayaran');
            });
        }

        // 3. Rename tabel purchasero_items → pembayaran_items
        if (Schema::hasTable('purchasero_items') && !Schema::hasTable('pembayaran_items')) {
            Schema::rename('purchasero_items', 'pembayaran_items');
        }

        // 4. Rename tabel purchasero_approvals → pembayaran_approvals
        if (Schema::hasTable('purchasero_approvals') && !Schema::hasTable('pembayaran_approvals')) {
            Schema::rename('purchasero_approvals', 'pembayaran_approvals');
        }

        // 5. Rename tabel purchasero_service_parts → pembayaran_service_parts
        if (Schema::hasTable('purchasero_service_parts') && !Schema::hasTable('pembayaran_service_parts')) {
            Schema::rename('purchasero_service_parts', 'pembayaran_service_parts');
        }

        // 6. Update foreign key column name: purchasero_id → pembayaran_id di pembayaran_items
        if (Schema::hasTable('pembayaran_items') && Schema::hasColumn('pembayaran_items', 'purchasero_id')) {
            Schema::table('pembayaran_items', function (Blueprint $table) {
                $table->renameColumn('purchasero_id', 'pembayaran_id');
            });
        }

        // 7. Update foreign key column name: purchasero_id → pembayaran_id di pembayaran_approvals
        if (Schema::hasTable('pembayaran_approvals') && Schema::hasColumn('pembayaran_approvals', 'purchasero_id')) {
            Schema::table('pembayaran_approvals', function (Blueprint $table) {
                $table->renameColumn('purchasero_id', 'pembayaran_id');
            });
        }

        // 8. Update foreign key column name: purchasero_id → pembayaran_id di pembayaran_service_parts
        if (Schema::hasTable('pembayaran_service_parts') && Schema::hasColumn('pembayaran_service_parts', 'purchasero_id')) {
            Schema::table('pembayaran_service_parts', function (Blueprint $table) {
                $table->renameColumn('purchasero_id', 'pembayaran_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse FK columns
        if (Schema::hasTable('pembayaran_service_parts') && Schema::hasColumn('pembayaran_service_parts', 'pembayaran_id')) {
            Schema::table('pembayaran_service_parts', function (Blueprint $table) {
                $table->renameColumn('pembayaran_id', 'purchasero_id');
            });
        }

        if (Schema::hasTable('pembayaran_approvals') && Schema::hasColumn('pembayaran_approvals', 'pembayaran_id')) {
            Schema::table('pembayaran_approvals', function (Blueprint $table) {
                $table->renameColumn('pembayaran_id', 'purchasero_id');
            });
        }

        if (Schema::hasTable('pembayaran_items') && Schema::hasColumn('pembayaran_items', 'pembayaran_id')) {
            Schema::table('pembayaran_items', function (Blueprint $table) {
                $table->renameColumn('pembayaran_id', 'purchasero_id');
            });
        }

        // Reverse: tipe_pembayaran → tipe_pengadaan
        if (Schema::hasTable('pembayarans') && Schema::hasColumn('pembayarans', 'tipe_pembayaran')) {
            Schema::table('pembayarans', function (Blueprint $table) {
                $table->renameColumn('tipe_pembayaran', 'tipe_pengadaan');
            });
        }

        // Reverse table renames
        if (Schema::hasTable('pembayaran_service_parts') && !Schema::hasTable('purchasero_service_parts')) {
            Schema::rename('pembayaran_service_parts', 'purchasero_service_parts');
        }

        if (Schema::hasTable('pembayaran_approvals') && !Schema::hasTable('purchasero_approvals')) {
            Schema::rename('pembayaran_approvals', 'purchasero_approvals');
        }

        if (Schema::hasTable('pembayaran_items') && !Schema::hasTable('purchasero_items')) {
            Schema::rename('pembayaran_items', 'purchasero_items');
        }

        if (Schema::hasTable('pembayarans') && !Schema::hasTable('purchaseros')) {
            Schema::rename('pembayarans', 'purchaseros');
        }
    }
};
