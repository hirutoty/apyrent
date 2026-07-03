<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceDetail extends Model
{
    use HasFactory;

    protected $table = 'service_detail';

    protected $fillable = [
    'kendaraan_id',
    'tanggal_service',
    'kilometer',
    'status',
    'biaya',
    'keterangan',
    'bukti',
];

    /**
     * Relasi ke service history
     */
    public function kendaraan()
{
    return $this->belongsTo(Kendaraan::class, 'kendaraan_id');
}
}