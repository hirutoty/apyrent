<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_pegawai',
        'gaji_pokok',
        'tunjangan',
        'thr',
        'bpjs',
        'pph21',
        'total_gaji',
        'slip_gaji',
    ];

    protected $casts = [
        'gaji_pokok'  => 'decimal:2',
        'tunjangan'   => 'decimal:2',
        'thr'         => 'decimal:2',
        'bpjs'        => 'decimal:2',
        'pph21'       => 'decimal:2',
        'total_gaji'  => 'decimal:2',
    ];
}
