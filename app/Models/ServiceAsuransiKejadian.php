<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceAsuransiKejadian extends Model
{
    use HasFactory;

    protected $table = 'service_asuransi_kejadians';

    protected $fillable = [
        'service_asuransi_id',
        'nama_kejadian',
        'tanggal',
        'periode_mulai',
        'periode_selesai',
        'biaya',
        'lampiran',
        'bukti_bayar',
        'status',
        'catatan_penolakan',
        'tanggal_approval',
    ];

    protected $casts = [
        'tanggal'          => 'date',
        'periode_mulai'    => 'date',
        'periode_selesai'  => 'date',
        'tanggal_approval' => 'datetime',
        'biaya'            => 'integer',
        'lampiran'         => 'array',
        'bukti_bayar'      => 'array',
    ];

    public function serviceAsuransi()
    {
        return $this->belongsTo(ServiceAsuransi::class, 'service_asuransi_id');
    }
}
