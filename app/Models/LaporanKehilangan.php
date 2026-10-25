<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanKehilangan extends Model
{
    use HasFactory;

    protected $table = 'laporan_kehilangans';

    protected $fillable = [
        'kehilangan_id',
        'laporan',
        'tanggal_laporan',
        'keterangan',
        'lokasi_pencarian',
        'file',
    ];

    protected $casts = [
        'tanggal_laporan' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    public function kehilangan()
    {
        return $this->belongsTo(Kehilangan::class);
    }
}
