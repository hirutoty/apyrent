<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembelianProyek extends Model
{
    protected $fillable = [
        'pr_no',
        'proyek',
        'item_diminta',
        'qty',
        'vendor',
        'estimasi_harga',
        'status',
        'tgl_permintaan',
    ];

    protected $casts = [
        'tgl_permintaan' => 'date',
    ];
}
