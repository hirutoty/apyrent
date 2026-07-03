<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvPenawaran extends Model
{
    use HasFactory;

    protected $table = 'inv_penawarans';

    protected $fillable = [
        'no_penawaran',
        'tanggal_penawaran',
        'kepada',
        'up',
        'perihal',

        // Kontak
        'customer_name',
        'contact_person',

        // Pengirim & Penandatangan
        'pengirim',
        'periode',
        'staff',
        'name_staff',
        'direktur',
        'name_direktur',

        'status',
        'total',
        'file_penawaran',
        'file_persyaratan',
    ];

    protected $casts = [
        'tanggal_penawaran' => 'date',
    ];

    /**
     * Detail item penawaran
     */
    public function items()
    {
        return $this->hasMany(InvPenawaranItem::class, 'penawaran_id');
    }

    public function kontrak()
    {
        return $this->hasOne(InvKontrak::class, 'penawaran_id');
    }

    public function invoice()
{
    return $this->hasOne(Invoice::class, 'penawaran_id');
}

   
}
