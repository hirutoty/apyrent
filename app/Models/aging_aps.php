<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class aging_aps extends Model
{
    use HasFactory;

    protected $table = 'aging_aps';

    protected $fillable = [
        'vendor',
        'no_tagihan',
        'jatuh_tempo',
        'jumlah',
        'kategori',
        'hutang_vendor_id',
        'status_lunas',
    ];

    protected $casts = [
        'jatuh_tempo' => 'date',
        'jumlah' => 'decimal:2',
        'status_lunas' => 'boolean',
    ];

    /**
     * Auto hitung umur jika tidak diisi (optional helper)
     */
    public function getUmurOtomatisAttribute()
    {
        return round(now()->diffInDays($this->jatuh_tempo, false));
    }

    /**
     * Relasi ke HutangVendor
     */
    public function hutangVendor()
    {
        return $this->belongsTo(HutangVendor::class, 'hutang_vendor_id');
    }
}
