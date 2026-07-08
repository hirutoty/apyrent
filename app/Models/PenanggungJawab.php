<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenanggungJawab extends Model
{
    use HasFactory;

    protected $table = 'penanggung_jawabs';

    protected $fillable = [
        'kode_aset',
        'nama_aset',
        'pic',
        'tanggal_penempatan',
        'divisi',
        'status',
    ];

    protected $casts = [
        'tanggal_penempatan' => 'date',
    ];
}
