<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'member';

    protected $fillable = [
        'nama_pelanggan',
        'kontak_pelanggan',
        'email_pelanggan',
        'alamat',
        'jenis_pelanggan',
    ];

    public function memberKendaraan()
    {
        return $this->hasMany(MemberKendaraan::class, 'member_id');
    }

    public function rentals()
    {
        return $this->hasMany(Rental::class, 'member_id');
    }

    public function agingAr()
    {
        return $this->hasMany(AgingAr::class, 'customer_id');
    }
}
