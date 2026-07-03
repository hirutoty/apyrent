<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class ServiceHistory extends Model
{
    use HasFactory;

    protected $table = 'service_history';

    protected $fillable = [
        'kendaraan_id',
        'keluhan',
        'kilometer',
        'total_biaya',
        'status',
        'tanggal_service',
        'sisa_limit',
        'maks_bulanan',
        'biaya_tahunan',
        'status_pengeluaran',
        'bukti_pembayaran',
    ];

    /**
     * Relasi ke kendaraan
     */
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    /**
     * Relasi ke detail service
     */
    public function details()
    {
        return $this->hasMany(ServiceDetail::class);
    }
    
}