<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServerCloud extends Model
{
    use HasFactory;

    protected $table = 'server_clouds';
    protected $guarded = [];

    protected $casts = [
        'backup_aktif' => 'boolean',
    ];
}
