<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkillMatrix extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_pegawai',
        'skill',
        'level',
        'sertifikasi',
        'evaluator',
        'tanggal_evaluasi',
    ];

    protected $casts = [
        'tanggal_evaluasi' => 'date',
        'level'            => 'integer',
    ];
}
