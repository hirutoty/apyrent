<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerolehanAsset extends Model
{
    use HasFactory;

    protected $table = 'perolehan_assets';

    protected $fillable = [
        'tanggal_perolehan',
        'kode_aset',
        'nama_aset',
        'vendor',
        'metode_pembelian',
        'harga',
        'status',
        'pembayaran',
    ];

    protected $casts = [
        'tanggal_perolehan' => 'date',
        'harga'             => 'decimal:2',
    ];
}
