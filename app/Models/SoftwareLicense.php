<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoftwareLicense extends Model
{
    use HasFactory;

    protected $table = 'software_licenses';
    protected $guarded = [];

    protected $casts = [
        'masa_berlaku'           => 'date',
        'tanggal_perpanjangan'   => 'date',
        'jumlah_lisensi'         => 'integer',
    ];
}
