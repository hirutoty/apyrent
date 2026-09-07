<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranItem extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_items';

    protected $fillable = [
        'pembayaran_id',
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
     * Relation to pembayaran (parent)
     */
    public function pembayaran()
    {
        return $this->belongsTo(Pembayaran::class);
    }
}
