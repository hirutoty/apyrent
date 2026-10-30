<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PajakKendaraan extends Model
{
    use HasFactory;

    protected $table = 'pajak_kendaraans';

    protected $fillable = [
        'kendaraan_id',
        'jenis_pajak',
        'nominal',
        'jatuh_tempo',
        'tanggal_bayar',
        'tanggal_buat',
        'status',
        'status_aktif',
        'keterangan',
        'bukti',
        'nama_pemilik',
        'nama_bank',
        'no_rekening',
        'pembayaran_id',
        'persetujuan',
        'tanggal_approval',    // ← Tambah: tanggal saat pembayaran disetujui
    ];

    protected $casts = [
        'jatuh_tempo' => 'date',
        'tanggal_bayar' => 'date',
        'tanggal_buat' => 'date',
        'nominal' => 'decimal:2',
        'tanggal_approval' => 'datetime',  // ← Cast sebagai datetime
    ];

    /**
     * Relasi ke kendaraan
     */
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class)->withTrashed();
    }

    public function histories()
    {
        return $this->hasMany(PajakHistory::class, 'pajak_kendaraan_id');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'relation_id')
            ->where('relation_type', 'pajak');
    }
}
