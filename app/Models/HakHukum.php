<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HakHukum extends Model
{
    protected $table = 'hak_hukums';

    protected $fillable = [
        'jenis_akses',
        'kategori_dokumen',
        'penerima_akses',
        'level_hak',
        'tanggal_akses',
        'status',
    ];
}
