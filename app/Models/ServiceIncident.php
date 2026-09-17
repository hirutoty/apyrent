<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceIncident extends Model
{
    use HasFactory;

    protected $table = 'service_incidents';

    protected $fillable = [
        'kendaraan_id',
        'keluhan',
        'keterangan',
        'kilometer',
        'total_biaya',
        'status',
        'tanggal_service',
        'bukti_pembayaran',
        'status_approval',
        'approval_by',
        'approval_at',
        'pembayaran_id',
        'purchase_order_id',
        'persetujuan',
    ];

    protected $casts = [
        'approval_at' => 'datetime',
    ];

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function parts()
    {
        return $this->hasMany(ServiceIncidentPart::class, 'service_incident_id');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'relation_id')
            ->where('relation_type', 'service_incident');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approval_by');
    }

    public function pembayaran()
    {
        return $this->belongsTo(\App\Models\Pembayaran::class, 'pembayaran_id');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(\App\Models\PurchaseOrder::class, 'purchase_order_id');
    }
}
