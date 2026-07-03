<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Gps extends Model
{
    use HasFactory;

    protected $table = 'gps';

    protected $fillable = [
        'user_id',
        'nama_gps',
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