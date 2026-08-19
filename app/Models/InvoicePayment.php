<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model
{
    use HasFactory;

    protected $table = 'invoice_payments';

    protected $fillable = [
        'invoice_id',
        'amount',
        'payment_date',
        'method',
        'transaction_id',
        'file_pembayaran',
        'file_pembayaran_name',
        'attachment',
        'status',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount'       => 'decimal:2',
        'attachment'   => 'array',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}