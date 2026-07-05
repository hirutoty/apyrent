<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekonsiliasiBank extends Model
{
    protected $table = 'rekonsiliasi_bank';

    protected $fillable = [
        'tanggal',
        'deskripsi',
        'reference_no',
        'amount',
        'currency',
        'status_rekonsiliasi',
        'invoice_id',
        'va',
        'bukti_pembayaran' 
    ];

    protected $casts = [
        'tanggal' => 'date',
        'amount' => 'decimal:2',
    ];

    // OPTIONAL
    // Relasi invoice jika ada model Invoice
    /*
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
    */
}