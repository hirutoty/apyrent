<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanKeuangan extends Model
{
    protected $table = 'laporan_keuangan';

    protected $fillable = [
        'nama_perusahaan',
        'pendapatan',
        'beban',
        'laba',
        'periode',
    ];
}