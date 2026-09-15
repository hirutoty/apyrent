<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Stub model — tabel purchaseros digantikan oleh PurchaseOrder.
 * Dipertahankan agar ServiceHistoryController tidak error saat di-load.
 */
class Purchasero extends Model
{
    protected $table = 'purchaseros';

    protected $fillable = [
        'no_pr', 'tanggal', 'departemen', 'pemohon', 'vendor',
        'alasan_permintaan', 'keterangan', 'nominal', 'status',
        'supplier_id', 'kendaraan_id',
    ];
}
