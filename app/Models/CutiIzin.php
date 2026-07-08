<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CutiIzin extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_pegawai',
        'jenis_cuti_izin',
        'tanggal_mulai',
        'tanggal_selesai',
        'lama_hari',
        'alasan',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'lama_hari'       => 'integer',
    ];
}
