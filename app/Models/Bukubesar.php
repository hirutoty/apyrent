<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bukubesar extends Model
{
    protected $table = 'bukubesars';

    protected $fillable = [

        'kode_jurnal',
        'transaksi',
        'kategori',
        'tanggal',
        'debit',
        'kredit',
        'saldo',
        'aktivitas',
        'keterangan',

    ];
}