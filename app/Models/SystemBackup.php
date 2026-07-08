<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemBackup extends Model
{
    use HasFactory;

    protected $table = 'system_backups';
    protected $guarded = [];

    protected $casts = [
        'uji_restore_terakhir' => 'date',
    ];
}
