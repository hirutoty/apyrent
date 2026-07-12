<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Attachment;

class GpsKendaraanHistory extends Model
{
    use HasFactory;

    protected $table = 'gps_kendaraan_histories';

    protected $fillable = [
        'gps_kendaraan_id',
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
        'diperpanjang_pada',
    ];

    protected $casts = [
        'tanggal_pasang'    => 'date',
        'tanggal_habis'     => 'date',
        'diperpanjang_pada' => 'datetime',
    ];

    /**
     * Relasi ke data GPS kendaraan aktif (sumber history ini)
     */
    public function gpsKendaraan()
    {
        return $this->belongsTo(GpsKendaraan::class);
    }

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

    /**
     * Relasi ke attachments yang diupload saat perpanjangan
     */
    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'relation_id')
                    ->where('relation_type', 'gps_history');
    }
}