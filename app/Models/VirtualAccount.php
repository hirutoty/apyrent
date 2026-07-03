<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VirtualAccount extends Model
{
    protected $fillable = [
        'va_number',
        'member_id',
        'invoice_id',
        'bukti_pembayaran',
        'bank',
        'expected_amount',
        'paid_amount',
        'status',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    
}