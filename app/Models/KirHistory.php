<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KirHistory extends Model
{
    use HasFactory;

    protected $table = 'kir_history';

    protected $fillable = [
        'kir_id',
        'kendaraan_id',
        'no_uji',
        'masa_berlaku',
        'biaya',
        'image',
        'tanggal_bayar',
        'diperpanjang_pada',
    ];

    protected $casts = [
        'masa_berlaku'      => 'date',
        'tanggal_bayar'     => 'date',
        'diperpanjang_pada' => 'datetime',
    ];

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function kir()
    {
        return $this->belongsTo(Kir::class);
    }
    

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'relation_id')
                    ->where('relation_type', 'kir_history');
    }
}