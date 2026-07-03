<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
use HasFactory;

protected $table = 'supplier';

protected $fillable = [
    'user_id',
    'nama_supplier',
    'no_telp',
    'nama_barang',
    'jumlah_barang',
    'harga_barang',
];

/**
 * Relasi ke user
 */
public function user()
{
    return $this->belongsTo(User::class);
}
}