<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Stub model — digantikan oleh alur PurchaseOrder baru.
 * Dipertahankan agar ServiceHistoryController tidak error saat di-load.
 */
class PurchaseroServicePart extends Model
{
    protected $table = 'purchasero_service_parts';

    protected $fillable = [
        'purchasero_id', 'kendaraan_id', 'category_id',
        'nama_part', 'part_number', 'serial_number', 'posisi',
        'biaya', 'keterangan',
    ];
}
