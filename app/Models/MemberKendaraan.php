<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberKendaraan extends Model
{
    use HasFactory;

    protected $table = 'member_kendaraan';

    protected $fillable = [
        'member_id',
        'kendaraan_id',
        'tanggal_sewa',
        'tanggal_kembali',
        'biaya_sewa',
        'status_sewa',
    ];

    /*
    |----------------------------------------------------------------------
    | RELASI
    |----------------------------------------------------------------------
    */

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    /*
    |----------------------------------------------------------------------
    | CASTING (biar lebih rapi)
    |----------------------------------------------------------------------
    */

    protected $casts = [
        'tanggal_sewa' => 'date',
        'tanggal_kembali' => 'date',
        'biaya_sewa' => 'integer',
    ];
}