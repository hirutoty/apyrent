<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $table = 'purchase_orders';

    protected $fillable = [
        'po_id',
        'tanggal_po',
        'vendor',
        'terkait_rfq',
        'total_barang',
        'total_harga',
        'status_po',
        'tanggal_kirim',
        'tanggal_terima',
        'catatan',
    ];

    protected $casts = [
        'tanggal_po'     => 'date',
        'tanggal_kirim'  => 'date',
        'tanggal_terima' => 'date',
        'total_barang'   => 'integer',
        'total_harga'    => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        // Generate po_id otomatis, contoh: PO-001, PO-002, dst.
        static::creating(function ($model) {
            if (empty($model->po_id)) {
                $last = self::orderBy('id', 'desc')->first();

                $lastNumber = $last && preg_match('/(\d+)$/', $last->po_id, $m)
                    ? (int) $m[1]
                    : 0;

                $nextNumber = $lastNumber + 1;

                $model->po_id = 'PO-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            }
        });
    }
}
