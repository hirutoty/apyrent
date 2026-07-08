<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndukAsset extends Model
{
    use HasFactory;

    protected $table = 'induk_assets';

    protected $fillable = [
        'kode_aset',
        'nama_aset',
        'kategori',
        'lokasi',
        'tanggal_perolehan',
        'harga_perolehan',
        'status',
        'pic',
        'umur_ekonomis',
        'metode_penyusutan',
    ];

    protected $casts = [
        'tanggal_perolehan' => 'date',
        'harga_perolehan'   => 'integer',
        'umur_ekonomis'     => 'integer',
    ];
}
