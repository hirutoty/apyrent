<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bupot extends Model
{
    use HasFactory;

    protected $table = 'bupot';

    protected $fillable = [
        'nomor_bukti',
        'tanggal_bukti',
        'tipe',
        'npwp_pemotong',
        'nama_pemotong',
        'npwp_dipotong',
        'nama_dipotong',
        'jumlah_bruto',
        'tarif_pajak',
        'jumlah_potong',
        'status',
        'file_bupot',
    ];
}