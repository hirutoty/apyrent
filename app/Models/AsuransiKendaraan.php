<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AsuransiKendaraan extends Model
{
    use HasFactory;

    protected $table = 'asuransi_kendaraan';

    protected $fillable = [
        'kendaraan_id',
        'asuransi_id',
        'jenis_asuransi_id',
        'status_kendaraan',
        'tgl_mulai',
        'tgl_berakhir',
        'durasi_bulan',
        'biaya',
        'bukti_bayar',
    ];

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_berakhir' => 'date',
        'biaya' => 'decimal:2',
    ];

    /**
     * Relasi ke kendaraan
     */
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    /**
     * Relasi ke asuransi
     */
    public function asuransi()
    {
        return $this->belongsTo(Asuransi::class);
    }

    public function jenisAsuransi()
    {
        return $this->belongsTo(JenisAsuransi::class, 'jenis_asuransi_id');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'relation_id')
            ->where('relation_type', 'asuransi');
    }

    
}
