<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestforQuotation extends Model
{
    use HasFactory;

    protected $table = 'requestfor_quotations';

    protected $fillable = [
        'id_rfq',
        'tanggal_rfq',
        'vendor',
        'kode_barang',
        'nama_barang',
        'kuantitas',
        'satuan',
        'harga_estimasi',
        'tanggal_kirim',
        'status_rfq',
        'catatan',
    ];

    protected $casts = [
        'tanggal_rfq'   => 'date',
        'tanggal_kirim' => 'date',
        'harga_estimasi' => 'integer',
        'kuantitas'      => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        // Generate id_rfq otomatis, contoh: RFQ-001, RFQ-002, dst.
        static::creating(function ($model) {
            if (empty($model->id_rfq)) {
                $last = self::orderBy('id', 'desc')->first();

                $lastNumber = $last && preg_match('/(\d+)$/', $last->id_rfq, $m)
                    ? (int) $m[1]
                    : 0;

                $nextNumber = $lastNumber + 1;

                $model->id_rfq = 'RFQ-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            }
        });
    }
}
