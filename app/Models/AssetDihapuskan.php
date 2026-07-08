<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetDihapuskan extends Model
{
    use HasFactory;

    protected $table = 'asset_dihapuskans';

    protected $fillable = [
        'kode_aset',
        'nama_aset',
        'tanggal_hapus',
        'alasan',
        'nilai_buku',
        'status_akhir',
    ];

    protected $casts = [
        'tanggal_hapus' => 'date',
        'nilai_buku'    => 'decimal:2',
    ];
}
