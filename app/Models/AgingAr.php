<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Pelanggan;

class AgingAr extends Model
{
    use HasFactory;

    protected $table = 'aging_ars';

    protected $fillable = [
        'member_id',
        'invoice_id',
        'jatuh_tempo',
        'total',
        'kategori',
        'status',
        'bukti',
    ];

    protected $casts = [
        'jatuh_tempo' => 'date',
    ];

    // =========================
    // UMUR (AUTO HITUNG)
    // =========================
    public function getUmurAttribute()
    {
        return (int) round(now()->startOfDay()->diffInDays(
            \Carbon\Carbon::parse($this->jatuh_tempo)->startOfDay(),
            false
        ));
    }

    // =========================
    // KATEGORI AUTO (OPTIONAL HELPER)
    // =========================
    public function getKategoriOtomatisAttribute()
    {
        $umur = now()->diffInDays($this->jatuh_tempo, false);
        $overdue = abs($umur);

        if ($umur > 0) {
            return 'Current';
        }

        if ($overdue <= 30) {
            return 'Overdue 1';
        } elseif ($overdue <= 60) {
            return 'Overdue 2';
        } elseif ($overdue <= 90) {
            return 'Overdue 3';
        }

        return 'Overdue 4';
    }

    public function member()
    {
        return $this->belongsTo(Pelanggan::class, 'member_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}