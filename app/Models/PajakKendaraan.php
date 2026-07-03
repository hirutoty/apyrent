<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PajakKendaraan extends Model
{
    use HasFactory;

    protected $table = 'pajak_kendaraans';

    protected $fillable = [
        'kendaraan_id',
        'jenis_pajak',
        'nominal',
        'jatuh_tempo',
        'tanggal_bayar',
        'status',
        'keterangan',
        'bukti',
    ];

    protected $casts = [
        'jatuh_tempo' => 'date',
        'tanggal_bayar' => 'date',
        'nominal' => 'decimal:2',
    ];

    /**
     * Relasi ke kendaraan
     */
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function histories()
{
    return $this->hasMany(PajakHistory::class,'pajak_kendaraan_id');
}
}