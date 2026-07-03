<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Efaktur extends Model
{
    protected $table = 'efakturs';

    protected $fillable = [

        'nomor_faktur',
        'tanggal_faktur',
        'tipe',
        'npwp_lawan',
        'nama_lawan',
        'dpp',
        'ppn',
        'ppnbm',
        'status',
        'file_faktur',
    ];
}