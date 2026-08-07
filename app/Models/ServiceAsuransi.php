<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceAsuransi extends Model
{
    use HasFactory;

    protected $table = 'service_asuransi';

    protected $fillable = [
        'kendaraan_id',
        'tanggal_service',
        'periode_mulai',
        'periode_selesai',
        'kilometer',
        'biaya',
        'keterangan',
        'bukti',
        'attachment',
    ];

    protected $casts = [
        'bukti'      => 'array',
        'attachment' => 'array',
    ];

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'kendaraan_id');
    }
}
