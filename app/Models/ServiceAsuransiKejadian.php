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
        'biaya',
        'lampiran',
    ];

    protected $casts = [
        'biaya'    => 'integer',
        'lampiran' => 'array',
    ];

    public function serviceAsuransi()
    {
        return $this->belongsTo(ServiceAsuransi::class, 'service_asuransi_id');
    }
}
