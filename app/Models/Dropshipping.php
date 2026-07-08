<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dropshipping extends Model
{
    use HasFactory;

    protected $table = 'dropshippings';

    protected $fillable = [
        'kode_transaksi',
        'tipe',
        'vendor',
        'barang',
        'jumlah',
        'satuan',
        'customer_akhir',
        'tanggal_kirim',
        'status',
    ];

    protected $casts = [
        'tanggal_kirim' => 'date',
        'jumlah'        => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        // Generate kode_transaksi otomatis, contoh: DS-001, DS-002, dst.
        static::creating(function ($model) {
            if (empty($model->kode_transaksi)) {
                $last = self::orderBy('id', 'desc')->first();

                $lastNumber = $last && preg_match('/(\d+)$/', $last->kode_transaksi, $m)
                    ? (int) $m[1]
                    : 0;

                $nextNumber = $lastNumber + 1;

                $model->kode_transaksi = 'DS-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            }
        });
    }
}
