<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnggaranProyek extends Model
{
    protected $table = 'anggaran_proyek';

    protected $fillable = [
        'proyek',
        'kategori',
        'budget',
        'realisasi',
        'sisa',
        'persen_terpakai',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'realisasi' => 'decimal:2',
        'sisa' => 'decimal:2',
        'persen_terpakai' => 'decimal:2',
    ];
}