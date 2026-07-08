<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SertifikasiPerizinan extends Model
{
    protected $table = 'sertifikasi_perizinans';

    protected $fillable = [
        'jenis',
        'nomor',
        'instansi',
        'berlaku_hingga',
        'status',
    ];
}
