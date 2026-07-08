<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumenProyek extends Model
{
    protected $fillable = [
        'proyek',
        'nama_dokumen',
        'tipe',
        'file',
        'status',
        'tanggal_upload',
    ];

    protected $casts = [
        'tanggal_upload' => 'date',
    ];
}
