<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_incident_parts', function (Blueprint $table) {
            // Kolom ini digunakan untuk melacak PO mana yang menghasilkan part ini,
            // sehingga saat PO ditolak dan diajukan ulang, hanya parts dari PO tersebut
            // yang dihapus — bukan parts dari PO sebelumnya yang sudah diapprove.
            $table->unsignedBigInteger('purchase_order_id')->nullable()->after('service_incident_id');
        });
    }

    public function down(): void
    {
        Schema::table('service_incident_parts', function (Blueprint $table) {
            $table->dropColumn('purchase_order_id');
        });
    }
};
