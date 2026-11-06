<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SerahTerimaBook extends Model
{
    use HasFactory;

    protected $table = 'serahterima_book';

    protected $fillable = [
        'id_rental',
        'tanggal',
        'file_masuk',
        'file_keluar',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function rental()
    {
        return $this->belongsTo(Rental::class, 'id_rental');
    }
}
