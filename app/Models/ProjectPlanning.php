<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectPlanning extends Model
{
    protected $fillable = [
        'kode_proyek',
        'tahapan',
        'tgl_mulai',
        'tgl_selesai',
        'durasi',
        'pic',
        'status',
    ];

    protected $casts = [
        'tgl_mulai'  => 'date',
        'tgl_selesai'=> 'date',
    ];
}
