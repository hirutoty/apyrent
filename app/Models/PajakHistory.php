<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use HasFactory;
use App\Models\Attachment;

class PajakHistory extends Model
{

    protected $fillable = [
        'pajak_kendaraan_id',
        'kendaraan_id',
        'jenis_pajak',
        'nominal',
        'jatuh_tempo',
        'tanggal_bayar',
        'tanggal_buat',
        'status',
        'keterangan',
        'bukti',
        'nama_pemilik',
        'nama_bank',
        'no_rekening',
        'diperpanjang_pada',
    ];

    protected $casts = [
        'jatuh_tempo'       => 'date',
        'tanggal_bayar'     => 'date',
        'tanggal_buat'      => 'date',
        'diperpanjang_pada' => 'datetime',
    ];

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function pajak()
    {
        return $this->belongsTo(PajakKendaraan::class,'pajak_kendaraan_id');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'relation_id')
                    ->where('relation_type', 'pajak_history');
    }
}