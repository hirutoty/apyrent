<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Litigasi extends Model
{
    protected $table = 'litigasis';

    protected $fillable = [
        'kasus',
        'lawan',
        'jenis_kasus',
        'status',
        'pengacara',
        'tanggal_sidang',
    ];
}
