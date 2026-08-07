<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataLeasing extends Model
{
    use HasFactory;

    protected $table = 'data_leasings';

    protected $fillable = [
        'kontrak_id',
        'no_kontrak',
        'mobil',
        'tahun',
        'nopol',
        'user_leasing',
        'angsuran_per_bulan',
        'jatuh_tempo',
        'periode_mulai',
        'periode_selesai',
        'personal_account',
        'sumber_dana_debit',
        'cara_bayar',
        'asuransi_leasing',
    ];

    protected $casts = [
        'jatuh_tempo'        => 'integer',
        'angsuran_per_bulan' => 'integer',
        'periode_mulai'      => 'date',
        'periode_selesai'    => 'date',
    ];

    public function kontrak()
    {
        return $this->belongsTo(InvKontrak::class, 'kontrak_id');
    }
}
