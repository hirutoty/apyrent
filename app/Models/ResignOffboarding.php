<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResignOffboarding extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_pegawai',
        'jabatan_terakhir',
        'tanggal_resign',
        'alasan',
        'status_offboarding',
        'serah_terima',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_resign' => 'date',
    ];
}
