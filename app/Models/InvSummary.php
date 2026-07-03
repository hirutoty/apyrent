<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvSummary extends Model
{
    use HasFactory;

    protected $table = 'inv_summaries';

    protected $fillable = [
        'penawaran_id',
        'kontrak_id',
        'invoice_id',
        'type',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'payment_status',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    public function penawaran()
    {
        return $this->belongsTo(InvPenawaran::class, 'penawaran_id');
    }

    public function kontrak()
    {
        return $this->belongsTo(InvKontrak::class, 'kontrak_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}