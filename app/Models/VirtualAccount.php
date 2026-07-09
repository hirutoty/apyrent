<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Pelanggan;

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
        'expired_at',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    
}