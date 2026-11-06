<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SerahTerimaKontrak extends Model
{
    use HasFactory;

    protected $table = 'serahterima_kontrak';

    protected $fillable = [
        'idkontrak',
        'id_rental',
        'tanggal',
        'file_masuk',
        'file_keluar',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function kontrak()
    {
        return $this->belongsTo(InvKontrak::class, 'idkontrak');
    }

    public function rental()
    {
        return $this->belongsTo(Rental::class, 'id_rental');
    }
}
