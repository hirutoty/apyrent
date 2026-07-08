<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorPricelist extends Model
{
    use HasFactory;

    protected $table = 'vendor_pricelists';

    protected $fillable = [
        'vendor',
        'kode_barang',
        'nama_barang',
        'harga_per_unit',
        'satuan',
        'diskon',
        'minimal_order',
        'lead_time',
        'tanggal_berlaku',
    ];

    protected $casts = [
        'tanggal_berlaku' => 'date',
        'harga_per_unit'  => 'integer',
        'minimal_order'   => 'integer',
        'lead_time'       => 'integer',
        'diskon'          => 'decimal:2',
    ];
}
