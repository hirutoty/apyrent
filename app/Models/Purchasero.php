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
        'tipe_pengadaan',
        'pemohon',
        'supplier_id',
        'barang_jasa',
        'kode_barang',
        'qty',
        'satuan',
        'alasan_permintaan',
        'keterangan',
        'nominal',
        'status',
        'disetujui_oleh',
        'tanggal_persetujuan',
        'catatan',
        'terakhir_diajukan',
        // Service fields
        'kendaraan_id',
        'tanggal_service',
        'kilometer',
        'keluhan',
        'bukti_pembayaran',
        'lampiran_tambahan',
        'nama_penerima',
        'nama_bank',
        'no_rekening',
    ];

    protected $casts = [
        'terakhir_diajukan' => 'datetime',
        'tanggal_service'   => 'date',
        'kilometer'         => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        // no_pr di-generate manual di store() untuk multi-item
    }

    /**
     * Cek apakah pengadaan ini tipe service
     */
    public function isService(): bool
    {
        return $this->tipe_pengadaan === 'service';
    }

    /**
     * Relation to Supplier
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Relation to Kendaraan (untuk pengadaan tipe service)
     */
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    /**
     * Accessor untuk nama supplier
     */
    public function getSupplierNameAttribute()
    {
        return $this->supplier ? $this->supplier->nama_supplier : null;
    }

    /**
     * Relation to items (multiple items per PR — untuk tipe belanja)
     */
    public function items()
    {
        return $this->hasMany(PurchaseroItem::class);
    }

    /**
     * Relation to service parts (untuk tipe service)
     */
    public function serviceParts()
    {
        return $this->hasMany(PurchaseroServicePart::class);
    }

    /**
     * Accessor for total_nominal
     */
    public function getTotalNominalAttribute()
    {
        if ($this->tipe_pengadaan === 'service') {
            if ($this->serviceParts && $this->serviceParts->isNotEmpty()) {
                return $this->serviceParts->sum('biaya');
            }
            return $this->attributes['nominal'] ?? 0;
        }

        if ($this->items && $this->items->isNotEmpty()) {
            return $this->items->sum('subtotal');
        }

        return $this->attributes['nominal'] ?? 0;
    }
}
