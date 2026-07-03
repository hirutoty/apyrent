<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stnk extends Model
{
    use HasFactory;

    protected $table = 'stnk';

    protected $fillable = [
        'kendaraan_id',
        'nopol',
        'merk',
        'nama_pemilik',
        'jenis_model',
        'masa_berlaku',
        'biaya',
        'bukti',
    ];

    /**
     * Relasi ke tabel kendaraan
     */
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    /**
     * Optional: format biaya biar rapi (Rp)
     */
    public function getBiayaFormatAttribute()
    {
        return 'Rp ' . number_format($this->biaya, 0, ',', '.');
    }

    /**
     * Optional: cek apakah STNK sudah expired
     */
    public function getIsExpiredAttribute()
    {
        return $this->masa_berlaku < now()->toDateString();
    }
}