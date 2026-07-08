<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DaftarNotaris extends Model
{
    protected $table = 'daftar_notaris';

    protected $fillable = [
        'nama_kantor',
        'layanan',
        'kontak',
        'email',
        'terakhir_dipakai',
    ];
}
