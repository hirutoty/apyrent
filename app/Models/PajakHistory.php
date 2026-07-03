<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use HasFactory;

class PajakHistory extends Model
{

    protected $fillable = [
        'pajak_kendaraan_id',
        'kendaraan_id',
        'jenis_pajak',
        'nominal',
        'jatuh_tempo',
        'tanggal_bayar',
        'status',
        'keterangan',
        'bukti',
        'diperpanjang_pada'
    ];

    protected $casts = [
        'jatuh_tempo'=>'date',
        'tanggal_bayar'=>'date',
        'diperpanjang_pada'=>'datetime'
    ];

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function pajak()
    {
        return $this->belongsTo(PajakKendaraan::class,'pajak_kendaraan_id');
    }
}