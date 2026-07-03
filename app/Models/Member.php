<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $table = 'member';

      protected $fillable = [
        'nama_member',
        'kontak_member',
        'email_member',
        'alamat',
        'jenis_member',
    ];

    public function memberKendaraan()
    {
        return $this->hasMany(MemberKendaraan::class);
    }

    public function rentals()
{
    return $this->hasMany(Rental::class);
}
}