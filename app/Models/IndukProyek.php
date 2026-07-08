<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndukProyek extends Model
{
    protected $fillable = [
        'kode',
        'nama_proyek',
        'jenis',
        'klien_vendor',
        'pic',
        'status',
        'mulai',
        'target_selesai',
        'progres',
        'nilai_proyek',
        'lokasi',
    ];

    protected $casts = [
        'mulai'         => 'date',
        'target_selesai'=> 'date',
    ];
}
