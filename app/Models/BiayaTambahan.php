<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiayaTambahan extends Model
{
    use HasFactory;

    /**
     * Nama tabel (opsional kalau tidak pakai plural default)
     */
    protected $table = 'biaya_tambahans';

    /**
     * Field yang boleh diisi (mass assignment)
     */
    protected $fillable = [
        'kendaraan_id',
        'nama_tambahan',
        'biaya',
    ];

    /**
     * Cast tipe data   
     */
    protected $casts = [
         'biaya' => 'decimal:2',
    ];

    /**
     * Relasi ke kendaraan
     */

    public function rentals()
    {
        return $this->belongsToMany(Rental::class, 'rental_biaya_tambahan');
    }

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }
}
