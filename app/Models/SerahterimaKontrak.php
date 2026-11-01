<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SerahterimaKontrak extends Model
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

    /**
     * Relasi ke DataKontrak
     */
    public function kontrak()
    {
        return $this->belongsTo(DataKontrak::class, 'idkontrak');
    }

    /**
     * Relasi ke Rental
     */
    public function rental()
    {
        return $this->belongsTo(Rental::class, 'id_rental');
    }
}
