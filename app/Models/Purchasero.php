<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchasero extends Model
{
    use HasFactory;

    protected $table = 'purchaseros';

    protected $fillable = [
        'no_pr',
        'tanggal',
        'departemen',
        'pemohon',
        'barang_jasa',
        'kode_barang',
        'qty',
        'satuan',
        'alasan_permintaan',
        'nominal',
        'status',
        'disetujui_oleh',
        'tanggal_persetujuan',
        'catatan',
        'terakhir_diajukan',
    ];

    protected $casts = [
        'terakhir_diajukan' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        // no_pr di-generate manual di store() untuk multi-item
        // agar semua item dalam 1 submit mendapat No PR yang sama
    }

    /**
     * Relation to items (multiple items per PR)
     */
    public function items()
    {
        return $this->hasMany(PurchaseroItem::class);
    }

    /**
     * Accessor for total_nominal from items
     */
    public function getTotalNominalAttribute()
    {
        // If has items, calculate from items sum
        if ($this->items && $this->items->isNotEmpty()) {
            return $this->items->sum('subtotal');
        }
        
        // Fallback to old nominal field for backward compatibility
        return $this->attributes['nominal'] ?? 0;
    }
}