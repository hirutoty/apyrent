<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectCost extends Model
{
    protected $fillable = [
        'proyek',
        'kategori_biaya',
        'estimasi',
        'realisasi',
        'selisih',
        'status',
    ];

    protected $casts = [
        'estimasi'  => 'decimal:2',
        'realisasi' => 'decimal:2',
        'selisih'   => 'decimal:2',
    ];
}
