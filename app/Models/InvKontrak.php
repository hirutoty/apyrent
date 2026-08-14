<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvKontrak extends Model
{
    use HasFactory;

    protected $table = 'inv_kontraks';

    protected $fillable = [
        'penawaran_id',
        'no_kontrak',
        'tanggal_kontrak',
        'perjanjian_pembayaran',
        'durasi_value',
        'durasi_satuan',
        'tanggal_selesai',

        'pihak_pertama',
        'contact_pertama',

        'pihak_kedua',
        'contact_kedua',
        'no_ktp_kedua',
        'email_kedua',
        'jenis_pelanggan',
        'alamat_kedua',

        'file_kontrak',
        'file_persyaratan',
        'file_draft',

        'status',
        'ketentuan_asuransi',
        'pasal_ketentuan',
        'ketentuan_id',
        'ketentuan_en',
    ];

    protected $casts = [
        'tanggal_kontrak'      => 'date',
        'perjanjian_pembayaran'=> 'date',
        'tanggal_selesai'      => 'date',
        'durasi_value'         => 'integer',
        'ketentuan_asuransi'   => 'array',
        'pasal_ketentuan'      => 'array',
    ];

    public function penawaran()
    {
        return $this->belongsTo(InvPenawaran::class, 'penawaran_id');
    }
    public function invoice()
{
    return $this->hasOne(Invoice::class, 'kontrak_id');
}
}