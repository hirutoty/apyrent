<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemeliharaanAsset extends Model
{
    use HasFactory;

    protected $table = 'pemeliharaan_assets';

    protected $fillable = [
        'kode_aset',
        'tanggal_service',
        'jenis_service',
        'vendor_pic',
        'biaya',
        'status',
        'jadwal_selanjutnya',
        'catatan',
    ];

    protected $casts = [
        'tanggal_service'    => 'date',
        'jadwal_selanjutnya' => 'date',
        'biaya'              => 'decimal:2',
    ];
}
