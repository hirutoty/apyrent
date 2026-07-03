<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'nama_perusahaan',
        'alamat',
        'telepon',
        'email',
        'website',
        'logo',
        'nama_bank',
        'nomor_rekening',
        'atas_nama_rekening',
        'kode_pos',
        'batas_reminder',
        'satuan_reminder',
    ];
}