<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeasingPayment extends Model
{
    protected $table = 'leasing_payments';

    protected $fillable = [
        'data_leasing_id',
        'bulan_cicilan',
        'nominal',
        'tanggal_catat',
        'keuangan_id',
    ];

    protected $casts = [
        'bulan_cicilan'  => 'date',
        'tanggal_catat'  => 'date',
        'nominal'        => 'integer',
    ];

    public function dataLeasing()
    {
        return $this->belongsTo(DataLeasing::class, 'data_leasing_id');
    }

    public function keuangan()
    {
        return $this->belongsTo(Keuangan::class, 'keuangan_id');
    }
}
