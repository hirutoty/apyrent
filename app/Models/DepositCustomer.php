<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepositCustomer extends Model
{
    protected $fillable = [
        'rental_id',
        'nominal',
        'potongan',
        'status',
        'tanggal_deposit',
        'keterangan',
    ];

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }
}