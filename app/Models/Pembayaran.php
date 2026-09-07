<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayarans';

    protected $fillable = [
        'no_pr',
        'tanggal',
        'departemen',
        'tipe_pembayaran',
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
        // Approval system fields
        'source_type',
        'source_data',
        'target_id',
        'can_edit',
    ];

    protected $casts = [
        'terakhir_diajukan' => 'datetime',
        'tanggal_service'   => 'date',
        'kilometer'         => 'integer',
        'source_data'       => 'array',
        'can_edit'          => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        // no_pr di-generate manual di store() untuk multi-item
    }

    /**
     * Cek apakah pembayaran ini tipe service
     */
    public function isService(): bool
    {
        return $this->tipe_pembayaran === 'service';
    }

    /**
     * Relation to Supplier
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Relation to Kendaraan (untuk pembayaran tipe service)
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
        return $this->hasMany(PembayaranItem::class);
    }

    /**
     * Relation to service parts (untuk tipe service)
     */
    public function serviceParts()
    {
        return $this->hasMany(PembayaranServicePart::class);
    }

    /**
     * Accessor for total_nominal
     */
    public function getTotalNominalAttribute()
    {
        if ($this->tipe_pembayaran === 'service') {
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

    /**
     * Relation to approval history
     */
    public function approvals()
    {
        return $this->hasMany(PembayaranApproval::class)->orderBy('created_at', 'desc');
    }

    /**
     * Relation to latest approval
     */
    public function latestApproval()
    {
        return $this->hasOne(PembayaranApproval::class)->latestOfMany();
    }

    /**
     * Check apakah ini pengeluaran (bukan belanja)
     */
    public function isPengeluaran(): bool
    {
        return !empty($this->source_type);
    }

    /**
     * Check apakah bisa diedit (status Ditolak dan can_edit = true)
     */
    public function canBeEdited(): bool
    {
        return $this->can_edit && $this->status === 'Ditolak';
    }

    /**
     * Check apakah pending approval
     */
    public function isPending(): bool
    {
        return $this->status === 'Pending';
    }

    /**
     * Check apakah sudah disetujui
     */
    public function isApproved(): bool
    {
        return $this->status === 'Disetujui';
    }

    /**
     * Check apakah ditolak
     */
    public function isRejected(): bool
    {
        return $this->status === 'Ditolak';
    }

    /**
     * Get decoded source data with defaults
     */
    public function getSourceDataDecodedAttribute(): array
    {
        return $this->source_data ?? [];
    }

    /**
     * Get human-readable source type name
     */
    public function getSourceTypeNameAttribute(): string
    {
        return match($this->source_type) {
            'asuransi_kendaraan' => 'Asuransi Kendaraan',
            'pajak' => 'Pajak Kendaraan',
            'service_part' => 'Service Part',
            'gps' => 'GPS Kendaraan',
            'kir' => 'KIR',
            'stnk' => 'STNK',
            'service_asuransi' => 'Service Asuransi',
            default => $this->tipe_pembayaran ?? 'Belanja',
        };
    }
}
