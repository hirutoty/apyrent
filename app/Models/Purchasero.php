<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchasero extends Model
{
    use HasFactory;

    protected $table = 'purchaseros';

    protected $fillable = [
        'no_pr',
        'tanggal',
        'departemen',
        'pemohon',
        'barang_jasa',
        'kode_barang',
        'qty',
        'satuan',
        'alasan_permintaan',
        'status',
        'disetujui_oleh',
        'tanggal_persetujuan',
        'catatan',
    ];

    protected static function boot()
    {
        parent::boot();

        // Generate no_pr otomatis & berurutan setiap kali data baru dibuat
        static::creating(function ($model) {
            if (empty($model->no_pr)) {
                $last = self::orderBy('id', 'desc')->first();

                $lastNumber = $last && preg_match('/(\d+)$/', $last->no_pr, $m)
                    ? (int) $m[1]
                    : 0;

                $nextNumber = $lastNumber + 1;

                $model->no_pr = 'PR-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            }
        });
    }
}