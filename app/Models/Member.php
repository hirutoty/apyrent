<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $table = 'members';

    protected $fillable = [
        'nama',
        'kontak',
        'email',
        'jenis_member',
        'alamat',
        'file_stnk',
        'file_attachment',
        'file_kontrak',
    ];

    protected $casts = [
        'file_stnk'       => 'array',
        'file_attachment' => 'array',
    ];

    public function kendaraans()
    {
        return $this->hasMany(Kendaraan::class, 'member_id');
    }
}
