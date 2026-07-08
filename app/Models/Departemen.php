<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departemen extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_departemen',
        'kepala_departemen',
        'tanggal_dibentuk',
        'jumlah_posisi',
        'keterangan',
        'status_aktif',
    ];

    protected $casts = [
        'tanggal_dibentuk' => 'date',
    ];
}
