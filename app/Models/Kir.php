<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kir extends Model
{
    use HasFactory;

    protected $table = 'kir';

    protected $fillable = [
        'kendaraan_id',
        'no_uji',
        'masa_berlaku',
        'biaya',
        'image',
    ];

    /**
     * Relasi ke kendaraan
     */
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }
}