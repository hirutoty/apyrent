<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asuransi extends Model
{
    use HasFactory;

    protected $table = 'asuransi';

    protected $fillable = [
        'user_id',
        'nama_asuransi',
        'alamat',
        'nama_marketing',
        'kontak_marketing',
        'nama_bengkel',
        'kontak_bengkel',
    ];

    /**
     * Relasi ke user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}