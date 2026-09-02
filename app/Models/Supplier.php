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
    'alamat',
    'nama_marketing',
    'kontak_marketing',
];

/**
 * Relasi ke user
 */
public function user()
{
    return $this->belongsTo(User::class);
}

/**
 * Relasi ke purchaseros (pengadaan)
 */
public function purchaseros()
{
    return $this->hasMany(Purchasero::class);
}
}
