<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorPerformance extends Model
{
    use HasFactory;

    protected $table = 'vendor_performances';

    protected $fillable = [
        'vendor',
        'total_order',
        'ketepatan_waktu',
        'kualitas_barang',
        'komplain',
        'penilaian_akhir',
        'catatan',
    ];

    protected $casts = [
        'total_order'      => 'integer',
        'komplain'         => 'integer',
        'ketepatan_waktu'  => 'decimal:2',
        'kualitas_barang'  => 'decimal:2',
        'penilaian_akhir'  => 'decimal:2',
    ];
}
