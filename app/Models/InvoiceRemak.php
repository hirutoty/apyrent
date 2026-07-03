<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceRemak extends Model
{
    use HasFactory;

    protected $table = 'invoice_remaks';

    protected $fillable = [
        'invoice_id',
        'periode_id',
        'remaks',
        'qty',
        'price',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function periode()
    {
        return $this->belongsTo(InvoicePeriode::class, 'periode_id');
    }
}