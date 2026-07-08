<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditAsset extends Model
{
    use HasFactory;

    protected $table = 'audit_assets';

    protected $fillable = [
        'kode_aset',
        'tanggal_audit',
        'diperiksa_oleh',
        'status_fisik',
        'temuan',
        'tindakan',
        'catatan',
    ];

    protected $casts = [
        'tanggal_audit' => 'date',
    ];
}
