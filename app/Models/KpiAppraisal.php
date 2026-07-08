<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiAppraisal extends Model
{
    use HasFactory;

    protected $table = 'kpi_appraisals';

    protected $fillable = [
        'nama_pegawai',
        'periode_evaluasi',
        'disiplin',
        'kolaborasi',
        'produktivitas',
        'nilai_akhir',
        'evaluator',
        'catatan',
    ];

    protected $casts = [
        'disiplin'      => 'integer',
        'kolaborasi'    => 'integer',
        'produktivitas' => 'integer',
        'nilai_akhir'   => 'decimal:2',
    ];
}
