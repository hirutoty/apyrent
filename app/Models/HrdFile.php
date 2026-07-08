<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrdFile extends Model
{
    use HasFactory;

    protected $table = 'hrd_files';

    protected $fillable = [
        'nama_pegawai',
        'nama_file',
        'jenis_dokumen',
        'file_path',
        'keterangan',
    ];
}
