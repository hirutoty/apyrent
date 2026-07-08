<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenyusutanAsset extends Model
{
    use HasFactory;

    protected $table = 'penyusutan_assets';

    protected $fillable = [
        'kode_aset',
        'tahun',
        'nilai_awal',
        'akumulasi_penyusutan',
        'nilai_buku',
        'metode',
        'status',
    ];

    protected $casts = [
        'nilai_awal'            => 'decimal:2',
        'akumulasi_penyusutan'  => 'decimal:2',
        'nilai_buku'            => 'decimal:2',
    ];
}
