<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StnkHistory extends Model
{
    use HasFactory;

    protected $table = 'stnk_histories';

    protected $fillable = [
        'stnk_id',
        'kendaraan_id',
        'nopol',
        'merk',
        'nama_pemilik',
        'jenis_model',
        'masa_berlaku',
        'biaya',
        'bukti',
        'diperpanjang_pada',
    ];

    protected $casts = [
        'masa_berlaku'      => 'date',
        'diperpanjang_pada' => 'datetime',
    ];

    /**
     * Relasi ke data STNK aktif (sumber history ini)
     */
    public function stnk()
    {
        return $this->belongsTo(Stnk::class);
    }

    /**
     * Relasi ke kendaraan
     */
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }
}