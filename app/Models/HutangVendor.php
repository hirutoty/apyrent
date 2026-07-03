<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HutangVendor extends Model
{
    protected $fillable = [
        'nama_vendor',
        'kategori',
        'nominal',
        'dibayar',
        'sisa',
        'jatuh_tempo',
        'status',
        'keterangan',
    ];
}