<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KontrakAktif extends Model
{
    protected $table = 'kontrak_aktifs';

    protected $fillable = [
        'kode_kontrak',
        'mitra',
        'nilai',
        'tgl_mulai',
        'tgl_selesai',
        'pic',
        'status',
        'perpanjangan',
    ];

    protected $casts = [
        'perpanjangan' => 'boolean',
        'nilai'        => 'integer',
    ];
}
