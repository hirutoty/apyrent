<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StrukturOrganisasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_jabatan',
        'nama_pegawai',
        'nip_id',
        'departemen',
        'atasan_langsung',
        'lokasi',
        'status_jabatan',
        'tanggal_mulai',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
    ];
}
