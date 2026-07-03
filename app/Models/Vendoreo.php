<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendoreo extends Model
{
    use HasFactory;

    protected $table = 'vendoreos';

    protected $fillable = [
        'kode_vendor',
        'nama_vendor',
        'kategori',
        'alamat',
        'pic_vendor',
        'no_telp',
        'produk_jasa',
        'rating',
        'status',
        'tanggal_terakhir_order',
        'catatan',
    ];

    protected static function boot()
    {
        parent::boot();

        // Generate kode_vendor otomatis & berurutan setiap kali data baru dibuat
        static::creating(function ($model) {
            if (empty($model->kode_vendor)) {
                $last = self::orderBy('id', 'desc')->first();

                $lastNumber = $last && preg_match('/(\d+)$/', $last->kode_vendor, $m)
                    ? (int) $m[1]
                    : 0;

                $nextNumber = $lastNumber + 1;

                $model->kode_vendor = 'VND-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            }
        });
    }
}