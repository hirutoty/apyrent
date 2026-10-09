<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $table = 'purchase_orders';

    protected $fillable = [
        'po_id',
        'tanggal_po',
        'vendor',
        'pemohon',
        'departemen',
        'terkait_rfq',
        'total_barang',
        'total_harga',
        'status_po',
        'tanggal_kirim',
        'tanggal_terima',
        'catatan',
        'keterangan',
        // Approval workflow fields
        'source_type',
        'source_data',
        'status',
        'disetujui_oleh',
        'tanggal_persetujuan',
        'catatan_approval',
        'pembayaran_id',
        'can_edit',
        'terakhir_diajukan',
    ];

    protected $casts = [
        'tanggal_po'          => 'date',
        'tanggal_kirim'       => 'date',
        'tanggal_terima'      => 'date',
        'total_barang'        => 'integer',
        'total_harga'         => 'integer',
        'source_data'         => 'array',
        'tanggal_persetujuan' => 'datetime',
        'terakhir_diajukan'   => 'datetime',
        'can_edit'            => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        // Generate po_id otomatis dengan format: PO-YYYY-XXX (PO-2026-001, PO-2026-002, dst.)
        static::creating(function ($model) {
            if (empty($model->po_id)) {
                $year = now()->year;
                $prefix = "PO-{$year}-";
                
                // Get last PO for current year
                $last = self::where('po_id', 'like', $prefix . '%')
                    ->orderBy('id', 'desc')
                    ->first();

                $lastNumber = 0;
                if ($last && preg_match('/PO-\d{4}-(\d+)$/', $last->po_id, $m)) {
                    $lastNumber = (int) $m[1];
                }

                $nextNumber = $lastNumber + 1;
                $model->po_id = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Relation to User (approver)
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    /**
     * Relation to Pembayaran (created after PO approved)
     */
    public function pembayaran()
    {
        return $this->belongsTo(Pembayaran::class, 'pembayaran_id');
    }

    /**
     * Check if PO is pending approval
     */
    public function isPending(): bool
    {
        return $this->status === 'Pending';
    }

    /**
     * Check if PO is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'Disetujui';
    }

    /**
     * Check if PO is rejected
     */
    public function isRejected(): bool
    {
        if ($this->status === 'Ditolak') return true;

        $sd = is_array($this->source_data) ? $this->source_data : (json_decode($this->source_data ?? '{}', true) ?? []);

        // Partial approval: status Disetujui tapi ada item_decisions dengan action=rejected
        if ($this->status === 'Disetujui'
            && !empty(array_filter($sd['item_decisions'] ?? [], fn($d) => ($d['action'] ?? '') === 'rejected'))) {
            return true;
        }

        // PO Pending setelah resubmit partial: item_decisions sudah dikosongkan tapi
        // locked_approved_idx masih ada → masih ada item yang perlu diajukan ulang
        if ($this->status === 'Pending' && !empty($sd['locked_approved_idx'] ?? [])) {
            return true;
        }

        return false;
    }

}