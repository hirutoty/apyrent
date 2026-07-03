<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvPenawaranItem extends Model
{
    use HasFactory;

    protected $table = 'inv_penawaran_items';

    protected $fillable = [
        'penawaran_id',
        'kendaraan_id',
        'qty',
        'tahun_unit',
        'price',
        'durasi',
        'satuan_durasi',
    ];

    public function penawaran()
    {
        return $this->belongsTo(InvPenawaran::class, 'penawaran_id');
    }

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'kendaraan_id');
    }
}