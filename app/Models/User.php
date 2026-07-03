<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Mass assignable
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'no_telp',
        'foto',
        'role',
        'status',
    ];

    /**
     * Hidden attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function asuransi()
    {
        return $this->hasMany(Asuransi::class);
    }

    public function gps()
    {
        return $this->hasMany(Gps::class);
    }

    public function service()
    {
        return $this->hasMany(Service::class);
    }

    public function supplier()
    {
        return $this->hasMany(Supplier::class);
    }
}