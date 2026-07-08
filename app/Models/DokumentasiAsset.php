<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumentasiAsset extends Model
{
    use HasFactory;

    protected $table = 'dokumentasi_assets';

    protected $fillable = [
        'kode_aset',
        'nama_aset',
        'foto_tersimpan',
        'lokasi_file',
        'catatan',
    ];

    protected $casts = [
        'foto_tersimpan' => 'boolean',
    ];
}
