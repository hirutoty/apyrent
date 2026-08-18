<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseroItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchasero_id',
        'nama_barang',
        'kategori',
        'posisi',
        'part_number',
        'serial_number',
        'qty',
        'satuan',
        'harga_satuan',
        'subtotal',
        'spesifikasi',
        'merk',
        'keterangan',
        'bukti',
    ];

    protected $casts = [
        'qty' => 'integer',
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'bukti' => 'array',
    ];

    /**
     * Relation to purchasero (parent)
     */
    public function purchasero()
    {
        return $this->belongsTo(Purchasero::class);
    }
}

