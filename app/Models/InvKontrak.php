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

        'pihak_pertama',
        'contact_pertama',

        'pihak_kedua',
        'contact_kedua',

        'file_kontrak',
        'file_persyaratan',

        'status',
    ];

    protected $casts = [
        'tanggal_kontrak' => 'date',
        'perjanjian_pembayaran' => 'date',
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