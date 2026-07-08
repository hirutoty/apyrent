<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegalDocument extends Model
{
    protected $table = 'legal_documents';

    protected $fillable = [
        'kode',
        'nama_dokumen',
        'jenis',
        'pihak_terkait',
        'tgl_terbit',
        'berlaku_hingga',
        'status',
        'format',
    ];
}
