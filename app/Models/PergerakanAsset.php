<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PergerakanAsset extends Model
{
    use HasFactory;

    protected $table = 'pergerakan_assets';

    protected $fillable = [
        'kode_aset',
        'tanggal',
        'jenis_pergerakan',
        'dari_lokasi',
        'ke_lokasi',
        'dilakukan_oleh',
        'disetujui_oleh',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];
}
