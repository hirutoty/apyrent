<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kir extends Model
{
    use HasFactory;

    protected $table = 'kir';

    protected $fillable = [
        'kendaraan_id',
        'no_ktp',
        'nama_ktp',
        'lokasi_uji',
        'penguji',
        'status_uji',
        'no_uji',
        'masa_berlaku',
        'biaya',
        'image',
        'tanggal_bayar',
        'tanggal_buat',
        'pembayaran_id',
        'persetujuan',
        'status',
        'status_aktif',        // ← Tambah: untuk tracking aktif/nonaktif/expired
        'tanggal_approval',    // ← Tambah: tanggal saat pembayaran disetujui
        'nama_bank',
        'no_rekening',
        'nama_rekening',
    ];

    protected $casts = [
        'masa_berlaku' => 'date',
        'tanggal_bayar' => 'date',
        'tanggal_buat' => 'date',
        'tanggal_approval' => 'datetime',  // ← Cast sebagai datetime
    ];

    /**
     * Relasi ke kendaraan
     */
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'relation_id')
            ->where('relation_type', 'kir');
    }

}