<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DendaRental extends Model
{
    protected $fillable = [
        'rental_id',
        'jenis',
        'nominal',
        'tanggal_denda',
        'keterangan',
    ];

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }
}