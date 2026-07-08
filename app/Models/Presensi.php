<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_pegawai',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'metode_presensi',
        'lokasi_presensi',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];
}
