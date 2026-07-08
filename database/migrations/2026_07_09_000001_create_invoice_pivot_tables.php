<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Pivot: invoice <-> penawaran (many-to-many)
        Schema::create('invoice_penawarans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('penawaran_id');
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->foreign('penawaran_id')->references('id')->on('inv_penawarans')->onDelete('cascade');
            $table->unique(['invoice_id', 'penawaran_id']);
        });

        // Pivot: invoice <-> kontrak (many-to-many)
        Schema::create('invoice_kontraks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('kontrak_id');
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->foreign('kontrak_id')->references('id')->on('inv_kontraks')->onDelete('cascade');
            $table->unique(['invoice_id', 'kontrak_id']);
        });

        // Pivot: invoice <-> kendaraan (many-to-many)
        Schema::create('invoice_kendaraans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('kendaraan_id');
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->foreign('kendaraan_id')->references('id')->on('kendaraan')->onDelete('cascade');
            $table->unique(['invoice_id', 'kendaraan_id']);
        });

        // Migrasi data lama dari kolom FK tunggal ke tabel pivot
        $invoices = DB::table('invoices')->whereNotNull('penawaran_id')
            ->orWhereNotNull('kontrak_id')
            ->orWhereNotNull('kendaraan_id')
            ->get();

        foreach ($invoices as $inv) {
            if ($inv->penawaran_id) {
                DB::table('invoice_penawarans')->insertOrIgnore([
                    'invoice_id'   => $inv->id,
                    'penawaran_id' => $inv->penawaran_id,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
            if ($inv->kontrak_id) {
                DB::table('invoice_kontraks')->insertOrIgnore([
                    'invoice_id' => $inv->id,
                    'kontrak_id' => $inv->kontrak_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            if ($inv->kendaraan_id) {
                DB::table('invoice_kendaraans')->insertOrIgnore([
                    'invoice_id'  => $inv->id,
                    'kendaraan_id' => $inv->kendaraan_id,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_kendaraans');
        Schema::dropIfExists('invoice_kontraks');
        Schema::dropIfExists('invoice_penawarans');
    }
};
