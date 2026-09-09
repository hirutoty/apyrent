<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class ServiceHistory extends Model
{
    use HasFactory;

    protected $table = 'service_history';

    protected $fillable = [
        'kendaraan_id',
        'keluhan',
        'kilometer',
        'total_biaya',
        'status',
        'tanggal_service',
        'sisa_limit',
        'maks_bulanan',
        'biaya_tahunan',
        'status_pengeluaran',
        'bukti_pembayaran',
        'status_approval',
        'approval_by',
        'approval_at',
        'is_request',
        'persetujuan',
    ];

    protected $casts = [
        'approval_at' => 'datetime',
    ];

    /**
     * Relasi ke kendaraan
     */
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    /**
     * Relasi ke detail service (Mobil Bermasalah — legacy)
     */
    public function details()
    {
        return $this->hasMany(ServiceDetail::class);
    }

    /**
     * Relasi ke parts (sistem baru)
     */
    public function parts()
    {
        return $this->hasMany(ServicePart::class, 'service_history_id');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'relation_id')
            ->where('relation_type', 'service');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approval_by');
    }
}
