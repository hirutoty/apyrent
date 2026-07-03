<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePeriode extends Model
{
    use HasFactory;

    protected $table = 'invoice_periodes';

    protected $fillable = [
        'invoice_id',
        'periode_awal',
        'periode_akhir',
    ];

    protected $casts = [
        'periode_awal' => 'date',
        'periode_akhir' => 'date',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function remaks()
    {
        return $this->hasMany(InvoiceRemak::class, 'periode_id');
    }
}