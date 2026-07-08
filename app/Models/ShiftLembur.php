<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftLembur extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_pegawai',
        'shift',
        'jam_masuk',
        'jam_pulang',
        'jam_lembur',
        'total_jam',
        'keterangan',
    ];
}
