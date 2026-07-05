<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aging_aps extends Model
{
    use HasFactory;

    protected $table = 'aging_aps';

    protected $fillable = [
        'vendor',
        'no_tagihan',
        'jatuh_tempo',
        'jumlah',
        'kategori',
    ];

    protected $casts = [
        'jatuh_tempo' => 'date',
        'jumlah' => 'integer',
    ];

    /**
     * Auto hitung umur jika tidak diisi (optional helper)
     */

    public function getUmurOtomatisAttribute()
{
    return round(now()->diffInDays($this->jatuh_tempo, false));
}
}
