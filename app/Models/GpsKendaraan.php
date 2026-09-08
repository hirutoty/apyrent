<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GpsKendaraan extends Model
{
    use HasFactory;

    protected $table = 'gps_kendaraan';

    protected $fillable = [
        'pembayaran_id',
        'kendaraan_id',
        'gps_id',
        'type',
        'status_gps',
        'tanggal_pasang',
        'tanggal_habis',
        'biaya_sewa',
        'durasi_bulan',
        'status_sewa',
        'bukti_bayar',
        'tanggal_bayar',
        'keterangan',
        'nama_bank',
        'no_rekening',
        'nama_pemilik',
        'persetujuan',
    ];

    /**
     * Relasi ke kendaraan
     */
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    /**
     * Relasi ke gps
     */
    public function gps()
    {
        return $this->belongsTo(Gps::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'relation_id')
            ->where('relation_type', 'gps');
    }
}
